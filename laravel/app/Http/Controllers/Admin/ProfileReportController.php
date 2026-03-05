<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfileReport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = ProfileReport::with(['reportedUser', 'reviewer'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->reason, fn ($q) => $q->where('reason', $request->reason))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => ProfileReport::count(),
            'pending' => ProfileReport::where('status', 'pending')->count(),
            'reviewed' => ProfileReport::where('status', 'reviewed')->count(),
            'rejected' => ProfileReport::where('status', 'rejected')->count(),
        ];

        return view('admin.reports.index', compact('reports', 'stats'));
    }

    public function updateStatus(Request $request, ProfileReport $report)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['reviewed', 'rejected'])],
        ]);

        $report->update([
            'status' => $validated['status'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('admin.reports.index')
            ->with('success', 'Status laporan berhasil diperbarui.');
    }
}

