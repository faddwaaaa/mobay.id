<?php
// SettingController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        // Simpan ke config/cache sesuai kebutuhan
        // Contoh: pakai spatie/laravel-settings atau simpel ke DB

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}