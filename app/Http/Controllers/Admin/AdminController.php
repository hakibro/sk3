<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AlasanBoyong;
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

        return view('admin.index', compact('users', 'alasans'));
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
}