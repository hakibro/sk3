<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Boyong;
use App\Services\BoyongService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoyongController extends Controller
{
    protected $boyongService;

    public function __construct(BoyongService $boyongService)
    {
        $this->boyongService = $boyongService;
    }

    /**
     * Menampilkan daftar pengajuan boyong
     * (Pusat melihat semua, Asrama melihat milik sendiri)
     */
    public function index()
    {
        $query = Boyong::with('siswa');

        if (Auth::user()->isAsrama()) {
            $query->where('asrama_asal', Auth::user()->lembaga);
        }

        $daftarBoyong = $query->latest()->paginate(20);
        return view('boyong.index', compact('daftarBoyong'));
    }

    /**
     * Form pengajuan boyong (Halaman detail siswa)
     */
    public function create($idperson)
    {
        $siswa = Siswa::findOrFail($idperson);

        // Cek kelayakan lewat Service
        $kelayakan = $this->boyongService->cekKelayakanBoyong($idperson);

        return view('boyong.create', compact('siswa', 'kelayakan'));
    }

    /**
     * Proses simpan pengajuan
     */
    public function store(Request $request)
    {
        $request->validate([
            'idperson' => 'required',
            'alasan' => 'required|string|min:5',
        ]);

        try {
            $this->boyongService->ajukanBoyong($request->idperson, $request->alasan);
            return redirect()->route('boyong.index')->with('success', 'Pengajuan boyong berhasil diproses.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Fitur Pengurus Pusat: Approve atau Reject
     */
    public function updateStatus(Request $request, $id)
    {
        // Hanya Pengurus Pusat yang bisa akses ini
        if (!Auth::user()->isPusat()) {
            abort(403, 'Hanya pengurus pusat yang dapat melakukan persetujuan.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'catatan_pusat' => 'nullable|string'
        ]);

        $boyong = Boyong::findOrFail($id);
        $boyong->update([
            'status' => $request->status,
            'catatan_pusat' => $request->catatan_pusat,
            'tgl_disetujui' => $request->status === 'approved' ? now() : null,
        ]);

        return back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    /**
     * Fitur Cetak Surat (Hanya jika status Approved)
     */
    public function cetakSurat($id)
    {
        $boyong = Boyong::with('siswa')->findOrFail($id);

        if ($boyong->status !== 'approved') {
            return back()->with('error', 'Surat hanya bisa dicetak jika sudah disetujui pusat.');
        }

        // Contoh return view untuk cetak (bisa dikembangkan ke PDF)
        return view('boyong.cetak', compact('boyong'));
    }
}