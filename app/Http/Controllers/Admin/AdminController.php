<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AlasanBoyong;
use App\Models\AppSetting;
use App\Models\Asrama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Dashboard Admin: Menampilkan daftar User dan Alasan
     */
    public function index()
    {
        $users = User::whereIn('role', ['pengurus_asrama', 'pengurus_pusat'])->get();
        $alasans = AlasanBoyong::all();
        $asramas = Asrama::select('asrama')->distinct()->orderBy('asrama')->pluck('asrama');
        $kopSurat = AppSetting::kopSurat();

        return view('admin.index', compact('users', 'alasans', 'asramas', 'kopSurat'));
    }

    // --- MANAJEMEN USER ---

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:pengurus_asrama,pengurus_pusat',
            'lembaga' => 'required_if:role,pengurus_asrama|nullable',
            'password' => 'required|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'lembaga' => $request->role === 'pengurus_asrama' ? $request->lembaga : null,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Pengurus berhasil ditambahkan.');
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'Pengurus berhasil dihapus.');
    }

    // --- MANAJEMEN ALASAN BOYONG ---

    public function storeAlasan(Request $request)
    {
        $request->validate([
            'nama_alasan' => 'required|string|unique:alasan_boyongs,nama_alasan',
        ]);

        AlasanBoyong::create($request->all());

        return back()->with('success', 'Alasan boyong baru berhasil ditambahkan.');
    }

    public function deleteAlasan($id)
    {
        AlasanBoyong::findOrFail($id)->delete();
        return back()->with('success', 'Alasan boyong berhasil dihapus.');
    }

    public function updateKopSurat(Request $request)
    {
        $validated = $request->validate([
            'nama_pesantren' => 'required|string|max:255',
            'nspp' => 'nullable|string|max:100',
            'alamat' => 'required|string|max:500',
        ]);

        AppSetting::setValue('kop_nama_pesantren', $validated['nama_pesantren']);
        AppSetting::setValue('kop_nspp', $validated['nspp'] ?? null);
        AppSetting::setValue('kop_alamat', $validated['alamat']);

        return back()->with('success', 'Pengaturan kop surat berhasil disimpan.');
    }
}
