<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfileReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProfileReportController extends Controller
{
    /**
     * Daftar laporan + statistik analitik lengkap
     */
    public function index(Request $request)
    {
        $query = ProfileReport::with(['reportedUser', 'reviewer'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->reason, fn ($q) => $q->where('reason', $request->reason))
            ->when($request->q, function ($q) use ($request) {
                $q->whereHas('reportedUser', fn ($u) =>
                    $u->where('name', 'like', "%{$request->q}%")
                      ->orWhere('username', 'like', "%{$request->q}%")
                );
            })
            ->when($request->reported_user, fn ($q) =>
                $q->where('reported_user_id', $request->reported_user)
            );

        match($request->sort) {
            'oldest'      => $query->oldest(),
            'most_report' => $query->orderByDesc(
                ProfileReport::selectRaw('count(*)')
                    ->whereColumn('reported_user_id', 'profile_reports.reported_user_id')
            ),
            default => $query->latest(),
        };

        $reports = $query->paginate(20)->withQueryString();

        $stats = [
            'total'    => ProfileReport::count(),
            'pending'  => ProfileReport::where('status', 'pending')->count(),
            'reviewed' => ProfileReport::where('status', 'reviewed')->count(),
            'rejected' => ProfileReport::where('status', 'rejected')->count(),
            'today'    => ProfileReport::whereDate('created_at', today())->count(),
            'unique_reported_users' => ProfileReport::distinct('reported_user_id')->count('reported_user_id'),
            'by_reason' => ProfileReport::selectRaw('reason, count(*) as cnt')
                ->groupBy('reason')->orderByDesc('cnt')
                ->pluck('cnt', 'reason')->toArray(),
            'daily_trend' => collect(range(6, 0))->map(function ($d) {
                $date  = now()->subDays($d);
                $count = ProfileReport::whereDate('created_at', $date)->count();
                return ['label' => $date->format('D'), 'count' => $count];
            })->toArray(),
        ];
        $stats['daily_trend_max'] = collect($stats['daily_trend'])->max('count') ?: 1;

        $highRiskAccounts = User::withCount('profileReports as reports_count')
            ->having('reports_count', '>=', 3)
            ->whereHas('profileReports', fn ($q) => $q->where('status', 'pending'))
            ->with(['profileReports' => fn ($q) => $q->select('id','reported_user_id','reason','status')->latest()->limit(20)])
            ->orderByDesc('reports_count')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                $user->report_reasons       = $user->profileReports->pluck('reason')->toArray();
                $user->latest_report_status = $user->profileReports->first()?->status;
                return $user;
            });

        return view('admin.reports.index', compact('reports', 'stats', 'highRiskAccounts'));
    }

    /**
     * Halaman detail satu laporan
     */
    public function show(ProfileReport $report)
    {
        $report->load(['reportedUser', 'reviewer']);

        $reportedUserTotalReports = ProfileReport::where('reported_user_id', $report->reported_user_id)->count();

        $prevReport = ProfileReport::where('id', '<', $report->id)->orderBy('id', 'desc')->first();
        $nextReport = ProfileReport::where('id', '>', $report->id)->orderBy('id', 'asc')->first();

        return view('admin.reports.show', compact(
            'report',
            'reportedUserTotalReports',
            'prevReport',
            'nextReport'
        ));
    }

    /**
     * Update status laporan (reviewed / rejected / pending)
     */
    public function updateStatus(Request $request, ProfileReport $report)
    {
        $request->validate([
            'status' => ['required', Rule::in(['reviewed', 'rejected', 'pending'])],
        ]);

        if (in_array($request->status, ['reviewed', 'rejected'])) {
            $report->update([
                'status'      => $request->status,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        } else {
            // Re-open ke pending
            $report->update([
                'status'      => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]);
        }

        // Kalau request datang dari halaman show, redirect balik ke show
        $referer = $request->headers->get('referer', '');
        if (str_contains($referer, '/reports/' . $report->id)) {
            return redirect()->route('admin.reports.show', $report)
                ->with('success', 'Status laporan berhasil diperbarui.');
        }

        return redirect()
            ->route('admin.reports.index', $request->query())
            ->with('success', 'Status laporan berhasil diperbarui.');
    }

    /**
     * Simpan catatan moderator (AJAX PATCH)
     */
    public function saveNote(Request $request, ProfileReport $report)
    {
        $request->validate(['note' => 'nullable|string|max:2000']);
        $report->update(['moderator_note' => $request->note]);
        return response()->json(['success' => true]);
    }

    /**
     * Serve satu file bukti dari private disk (aman, tidak bisa diakses publik)
     */
    public function evidenceFile(ProfileReport $report, int $index)
    {
        $paths = $report->evidence_paths ?? [];

        if (is_string($paths) && !empty($paths)) {
            $paths = json_decode($paths, true) ?? [];
        }

        if (!is_array($paths)) {
            $paths = [];
        }

        if (!isset($paths[$index])) {
            abort(404, 'Bukti tidak ditemukan.');
        }

        $path = $paths[$index];

        if (!Storage::disk('private')->exists($path)) {
            abort(404, 'File tidak ada di storage.');
        }

        $absolutePath = Storage::disk('private')->path($path);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mimeType = Storage::disk('private')->mimeType($path) ?: match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'mp4' => 'video/mp4',
            'mov' => 'video/quicktime',
            default => 'application/octet-stream',
        };

        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * Unduh semua bukti — 1 file langsung stream, >1 file dibuat ZIP
     */
    public function viewEvidence(ProfileReport $report)
    {
        $paths = $report->evidence_paths ?? [];

        if (is_string($paths) && !empty($paths)) {
            $paths = json_decode($paths, true) ?? [];
        }

        if (!is_array($paths)) {
            $paths = [];
        }

        if (empty($paths)) {
            abort(404, 'Tidak ada bukti untuk laporan ini.');
        }

        if (count($paths) === 1) {
            $path = $paths[0];
            if (!Storage::disk('private')->exists($path)) abort(404);
            return response()->streamDownload(
                fn () => print(Storage::disk('private')->get($path)),
                basename($path),
                ['Content-Type' => Storage::disk('private')->mimeType($path)]
            );
        }

        // Buat ZIP (ZipArchive sudah built-in PHP, tidak perlu install)
        $dir     = storage_path('app/temp');
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $zipPath = $dir . '/bukti_' . $report->ticket_code . '_' . time() . '.zip';

        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        foreach ($paths as $i => $path) {
            if (Storage::disk('private')->exists($path)) {
                $zip->addFromString(
                    'bukti_' . ($i + 1) . '_' . basename($path),
                    Storage::disk('private')->get($path)
                );
            }
        }
        $zip->close();

        return response()
            ->download($zipPath, 'bukti_' . $report->ticket_code . '.zip')
            ->deleteFileAfterSend(true);
    }

    /**
     * Export CSV
     */
    public function exportCsv(Request $request)
    {
        $reports = ProfileReport::with('reportedUser', 'reviewer')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->get();

        $filename = 'laporan-akun-' . now()->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($reports) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Ticket','Waktu','Dilaporkan','Username','Kategori','Deskripsi','IP','Status','Reviewer','Reviewed At']);
            foreach ($reports as $r) {
                fputcsv($out, [
                    $r->ticket_code,
                    $r->created_at->format('Y-m-d H:i:s'),
                    $r->reportedUser?->name,
                    $r->reportedUser?->username,
                    $r->reason,
                    $r->detail,
                    $r->reporter_ip,
                    $r->status,
                    $r->reviewer?->name,
                    $r->reviewed_at?->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
