<?php
// ================================================================
// FILE: app/Http/Controllers/Admin/SuspendController.php
// ================================================================

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProfileReport;
use Illuminate\Http\Request;

class SuspendController extends Controller
{
    /**
     * Suspend akun user.
     */
    public function suspend(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menangguhkan akun sendiri.');
        }
        if ($user->role === 'admin') {
            return back()->with('error', 'Akun admin tidak dapat ditangguhkan.');
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $user->update(['is_suspended' => 1]);

        // Jika dipanggil dari halaman detail laporan, update status laporan sekaligus
        if ($request->report_id) {
            $report = ProfileReport::find($request->report_id);
            if ($report && $report->reported_user_id === $user->id) {
                $report->update([
                    'status'         => 'reviewed',
                    'reviewed_by'    => auth()->id(),
                    'reviewed_at'    => now(),
                    'moderator_note' => '[SUSPEND] ' . $request->reason,
                ]);
            }
        }

        $redirect = ($request->redirect_to === 'report' && $request->report_id)
            ? route('admin.reports.show', $request->report_id)
            : route('admin.dashboard');

        return redirect($redirect)
            ->with('success', "Akun {$user->name} (@{$user->username}) berhasil ditangguhkan.");
    }

    /**
     * Cabut suspend akun user.
     */
    public function unsuspend(Request $request, User $user)
    {
        $user->update(['is_suspended' => 0]);

        $redirect = ($request->redirect_to === 'report' && $request->report_id)
            ? route('admin.reports.show', $request->report_id)
            : route('admin.dashboard');

        return redirect($redirect)
            ->with('success', "Penangguhan akun {$user->name} (@{$user->username}) berhasil dicabut.");
    }
}