<?php

namespace App\Http\Controllers;

use App\Models\AlasanBoyong;
use App\Models\AppSetting;
use App\Models\Boyong;
use App\Models\Siswa;
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
        $query = Boyong::with(['siswa', 'pengurus', 'validator']);

        if (Auth::user()->isAsrama()) {
            $query->where('asrama_asal', Auth::user()->lembaga);
        }

        $daftarBoyong = $query->latest()->paginate(20);

        return view('boyong.index', compact('daftarBoyong'));
    }

    public function laporan(Request $request)
    {
        if (! Auth::user()->isAsrama() && ! Auth::user()->isPusat()) {
            abort(403, 'Hanya pengurus asrama atau pengurus pusat yang dapat melihat laporan boyong.');
        }

        $query = Boyong::with(['siswa', 'pengurus', 'validator']);

        if (Auth::user()->isAsrama()) {
            $query->where('asrama_asal', Auth::user()->lembaga);
        } elseif ($request->filled('asrama')) {
            $query->where('asrama_asal', $request->asrama);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $summaryBase = clone $query;
        $summary = [
            'total' => (clone $summaryBase)->count(),
            'pending' => (clone $summaryBase)->where('status', 'pending')->count(),
            'approved' => (clone $summaryBase)->where('status', 'approved')->count(),
            'rejected' => (clone $summaryBase)->where('status', 'rejected')->count(),
            'perlu_cut' => (clone $summaryBase)->where('pembayaran_belum_lunas', true)->count(),
            'total_tagihan_cut' => (clone $summaryBase)->sum('total_tagihan_saat_pengajuan'),
        ];

        $asramas = Boyong::query()
            ->whereNotNull('asrama_asal')
            ->distinct()
            ->orderBy('asrama_asal')
            ->pluck('asrama_asal');

        $daftarBoyong = $query->latest()->paginate(25)->withQueryString();

        return view('boyong.laporan', compact('daftarBoyong', 'summary', 'asramas'));
    }

    /**
     * Form pengajuan boyong (Halaman detail siswa)
     */
    public function create($idperson)
    {
        if (! Auth::user()->isAsrama() && ! Auth::user()->isPusat()) {
            abort(403, 'Hanya pengurus asrama atau pengurus pusat yang dapat membuat SK3.');
        }

        $siswa = Siswa::findOrFail($idperson);

        if (Auth::user()->isAsrama() && $siswa->asrama !== Auth::user()->lembaga) {
            abort(403, 'Anda tidak memiliki akses ke santri di asrama lain.');
        }

        // Cek kelayakan lewat Service
        $kelayakan = $this->boyongService->cekKelayakanBoyong($idperson);
        $alasans = AlasanBoyong::orderBy('nama_alasan')->get();

        return view('boyong.create', compact('siswa', 'kelayakan', 'alasans'));
    }

    /**
     * Proses simpan pengajuan
     */
    public function store(Request $request)
    {
        if (! Auth::user()->isAsrama() && ! Auth::user()->isPusat()) {
            abort(403, 'Hanya pengurus asrama atau pengurus pusat yang dapat membuat SK3.');
        }

        $request->validate([
            'idperson' => 'required',
            'tanggal_boyong' => 'required|date',
            'alasan_kategori' => 'required|string|max:255',
            'alasan_lainnya' => 'required_if:alasan_kategori,Lainnya|nullable|string|max:255',
            'alasan_detail' => 'required|string|min:5',
            'kos_makan_bulan_berjalan' => 'nullable|integer|min:0',
            'boyong_scope' => 'nullable|array',
            'boyong_scope.asrama' => 'sometimes|boolean',
            'boyong_scope.madin' => 'sometimes|boolean',
            'boyong_scope.formal' => 'sometimes|boolean',
        ]);

        try {
            $boyong = $this->boyongService->ajukanBoyong($request->idperson, $request->all());
            $message = $boyong->status === 'approved'
                ? 'Surat boyong berhasil dibuat dan siap dicetak.'
                : 'Pengajuan boyong berhasil dikirim ke pengurus pusat.';

            return redirect()->route('boyong.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Fitur Pengurus Pusat: Approve atau Reject
     */
    public function updateStatus(Request $request, $id)
    {
        // Hanya Pengurus Pusat yang bisa akses ini
        if (! Auth::user()->isPusat()) {
            abort(403, 'Hanya pengurus pusat yang dapat melakukan persetujuan.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'catatan_pusat' => 'nullable|string',
        ]);

        $boyong = Boyong::findOrFail($id);

        try {
            if ($request->status === 'approved') {
                $this->boyongService->setujui($boyong, $request->catatan_pusat);
            } else {
                $this->boyongService->tolak($boyong, $request->catatan_pusat);
            }

            return back()->with('success', 'Status pengajuan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Fitur Cetak Surat (Hanya jika status Approved)
     */
    public function cetakSurat($id)
    {
        $boyong = Boyong::with(['siswa', 'pengurus', 'validator'])->findOrFail($id);

        if (Auth::user()->isAsrama() && $boyong->asrama_asal !== Auth::user()->lembaga) {
            abort(403, 'Anda tidak memiliki akses ke surat ini.');
        }

        if ($boyong->status !== 'approved') {
            return back()->with('error', 'Surat hanya bisa dicetak jika sudah disetujui pusat.');
        }

        // Cetak SK3 hanya bila seluruh sisa tagihan aktif telah lunas (sisa potongan kos makan/SPP).
        $sisaTagihanSaatIni = $this->boyongService->getTotalBelumLunasSaatIni($boyong->idperson);

        if ($sisaTagihanSaatIni > 0) {
            return back()->with('error', 'SK3 belum bisa dicetak karena masih ada sisa tagihan Rp '.number_format($sisaTagihanSaatIni, 0, ',', '.').'. Lunasi sisa potongan (kos makan/SPP) terlebih dahulu.');
        }

        if (! $boyong->public_token) {
            $boyong->update(['public_token' => $this->boyongService->generatePublicToken()]);
        }

        $kopSurat = AppSetting::kopSurat();

        return view('boyong.cetak', compact('boyong', 'kopSurat'));
    }

    public function cetakKeteranganPengajuan($id)
    {
        $boyong = Boyong::with(['siswa', 'pengurus', 'validator'])->findOrFail($id);

        if (Auth::user()->isAsrama() && $boyong->asrama_asal !== Auth::user()->lembaga) {
            abort(403, 'Anda tidak memiliki akses ke surat ini.');
        }

        $sisaTagihanSaatIni = $this->boyongService->getTotalBelumLunasSaatIni($boyong->idperson);

        if ($sisaTagihanSaatIni <= 0 && ! $boyong->pembayaran_belum_lunas) {
            return back()->with('error', 'Surat keterangan pengajuan hanya diperlukan untuk pengajuan yang masih memiliki catatan tagihan.');
        }

        $kopSurat = AppSetting::kopSurat();
        $snapshotTagihan = collect($boyong->snapshot_tagihan['detail_belum_lunas'] ?? []);

        return view('boyong.keterangan-pengajuan', compact('boyong', 'kopSurat', 'snapshotTagihan', 'sisaTagihanSaatIni'));
    }

    public function verifikasi(string $token)
    {
        $boyong = Boyong::with(['siswa', 'pengurus', 'validator'])
            ->where('public_token', $token)
            ->where('status', 'approved')
            ->firstOrFail();

        return view('boyong.verifikasi', compact('boyong'));
    }

    public function apiTagihanTerakhir($idperson)
    {
        $token = config('services.boyong_payment_api.token');

        if ($token && ! hash_equals($token, (string) request()->bearerToken())) {
            return response()->json([
                'success' => false,
                'message' => 'Token API tidak valid.',
            ], 401);
        }

        $boyong = Boyong::with('siswa')
            ->where('idperson', $idperson)
            ->whereIn('status', ['pending', 'approved'])
            ->latest()
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->boyongService->buatPayloadTagihanTerakhir($boyong),
        ]);
    }
}
