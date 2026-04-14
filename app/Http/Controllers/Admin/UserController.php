<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('username', 'like', "%{$request->search}%");
            })
            ->when($request->status === 'suspended', fn($q) => $q->where('is_suspended', true))
            ->when($request->status === 'active',    fn($q) => $q->where('is_suspended', false))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        // Sesuaikan relasi dengan model kamu
        // $user->load('links');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:user,admin',
        ]);

        $user->update($request->only('name', 'email', 'role'));

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        // Jangan hapus diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function suspend(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa suspend akun sendiri.');
        }

        $user->update(['is_suspended' => true]);

        return back()->with('success', "User @{$user->username} berhasil disuspend.");
    }

    public function unsuspend(User $user)
    {
        $user->update(['is_suspended' => false]);

        return back()->with('success', "User @{$user->username} berhasil diaktifkan kembali.");
    }
}