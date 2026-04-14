<?php
// ================================================================
// FILE: app/Http/Controllers/AppealController.php  (BARU)
// Controller untuk user yang disuspend mengajukan banding
// ================================================================

namespace App\Http\Controllers;

use App\Models\AccountAppeal;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppealController extends Controller
{
    /**
     * Simpan pengajuan banding dari halaman /suspended
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // Hanya user yang disuspend yang bisa banding
        if (!$user->is_suspended) {
            return response()->json(['success' => false, 'message' => 'Akun Anda tidak dalam status penangguhan.'], 403);
        }

        // Cek apakah sudah ada banding pending/approved
        $existing = AccountAppeal::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->latest()
            ->first();

        if ($existing) {
            $msg = $existing->status === 'pending'
                ? 'Anda sudah memiliki pengajuan banding yang sedang menunggu tinjauan (kode: ' . $existing->ticket_code . ').'
                : 'Banding Anda sebelumnya sudah disetujui.';
            return response()->json(['success' => false, 'message' => $msg], 422);
        }

        $validated = $request->validate([
            'reason'          => 'required|string|min:30|max:2000',
            'additional_info' => 'nullable|string|max:1000',
            'evidence'        => 'nullable|array|max:3',
            'evidence.*'      => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $evidencePaths = [];
        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                $folder = 'appeals/' . $user->id . '/' . date('Ymd');
                $path = $file->store($folder, 'private');
                if ($path) {
                    $evidencePaths[] = $path;
                }
            }
        }

        // Generate tiket unik
        do {
            $ticket = 'APL-' . strtoupper(Str::random(8));
        } while (AccountAppeal::where('ticket_code', $ticket)->exists());

        AccountAppeal::create([
            'user_id'         => $user->id,
            'ticket_code'     => $ticket,
            'reason'          => $validated['reason'],
            'additional_info' => $validated['additional_info'] ?? null,
            'evidence_paths'  => !empty($evidencePaths) ? $evidencePaths : null,
            'evidence_count'  => count($evidencePaths),
            'status'          => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Banding berhasil diajukan.',
            'ticket'  => $ticket,
        ]);
    }

    /**
     * Cek status banding milik user yang sedang login
     */
    public function status()
    {
        $user   = auth()->user();
        $appeal = AccountAppeal::where('user_id', $user->id)->latest()->first();

        return response()->json([
            'has_appeal' => (bool) $appeal,
            'appeal'     => $appeal ? [
                'ticket_code' => $appeal->ticket_code,
                'status'      => $appeal->status,
                'created_at'  => $appeal->created_at->format('d M Y, H:i'),
                'admin_note'  => $appeal->admin_note,
                'evidence_count' => $appeal->evidence_count ?? 0,
            ] : null,
        ]);
    }
}
