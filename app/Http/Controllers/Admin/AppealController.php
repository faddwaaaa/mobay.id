<?php
// ================================================================
// FILE: app/Http/Controllers/Admin/AppealController.php  (BARU)
// Controller untuk admin memproses banding
// ================================================================

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountAppeal;
use App\Models\ProfileReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppealController extends Controller
{
    /**
     * Daftar semua banding
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $appeals = AccountAppeal::with('user', 'reviewer')
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(5)
            ->withQueryString();

        $counts = [
            'pending'  => AccountAppeal::where('status', 'pending')->count(),
            'approved' => AccountAppeal::where('status', 'approved')->count(),
            'rejected' => AccountAppeal::where('status', 'rejected')->count(),
            'all'      => AccountAppeal::count(),
        ];

        return view('admin.appeals.index', compact('appeals', 'status', 'counts'));
    }

    /**
     * Detail banding
     */
    public function show(AccountAppeal $appeal)
    {
        $appeal->load('user', 'reviewer');

        $relatedReports = ProfileReport::with('reviewer')
            ->where('reported_user_id', $appeal->user_id)
            ->where('evidence_count', '>', 0)
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.appeals.show', compact('appeal', 'relatedReports'));
    }

    public function evidenceFile(AccountAppeal $appeal, int $index)
    {
        $paths = $appeal->evidence_paths ?? [];

        if (!isset($paths[$index])) {
            abort(404, 'Bukti banding tidak ditemukan.');
        }

        $path = $paths[$index];

        if (!Storage::disk('private')->exists($path)) {
            abort(404, 'File bukti tidak ada di storage.');
        }

        return response(
            Storage::disk('private')->get($path),
            200,
            ['Content-Type' => Storage::disk('private')->mimeType($path)]
        );
    }

    public function viewEvidence(AccountAppeal $appeal)
    {
        $paths = $appeal->evidence_paths ?? [];

        if (empty($paths)) {
            abort(404, 'Tidak ada bukti untuk banding ini.');
        }

        if (count($paths) === 1) {
            $path = $paths[0];
            if (!Storage::disk('private')->exists($path)) {
                abort(404);
            }

            return response()->streamDownload(
                fn () => print(Storage::disk('private')->get($path)),
                basename($path),
                ['Content-Type' => Storage::disk('private')->mimeType($path)]
            );
        }

        $dir = storage_path('app/temp');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $zipPath = $dir . '/bukti_banding_' . $appeal->ticket_code . '_' . time() . '.zip';
        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach ($paths as $i => $path) {
            if (Storage::disk('private')->exists($path)) {
                $zip->addFromString(
                    'bukti_banding_' . ($i + 1) . '_' . basename($path),
                    Storage::disk('private')->get($path)
                );
            }
        }

        $zip->close();

        return response()
            ->download($zipPath, 'bukti_banding_' . $appeal->ticket_code . '.zip')
            ->deleteFileAfterSend(true);
    }

    /**
     * Setujui banding → cabut suspend
     */
    public function approve(Request $request, AccountAppeal $appeal)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        // Cabut suspend
        $appeal->user->update(['is_suspended' => 0]);

        // Update status banding
        $appeal->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_note'  => $request->admin_note,
        ]);

        return back()->with('success', "Banding {$appeal->ticket_code} disetujui. Akun @{$appeal->user->username} telah dipulihkan.");
    }

    /**
     * Tolak banding → akun tetap suspend
     */
    public function reject(Request $request, AccountAppeal $appeal)
    {
        $request->validate([
            'admin_note' => 'required|string|max:1000',
        ]);

        $appeal->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_note'  => $request->admin_note,
        ]);

        return back()->with('success', "Banding {$appeal->ticket_code} ditolak.");
    }
}
