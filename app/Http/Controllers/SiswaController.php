<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Asrama;
use App\Services\PembayaranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    protected $pembayaranService;

    public function __construct(PembayaranService $pembayaranService)
    {
        $this->pembayaranService = $pembayaranService;
    }

    /**
     * Menampilkan daftar siswa dengan filter akses asrama
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Memulai query dari View v_siswa
        $query = Siswa::query();

        // Gabungkan logika filter asrama
        if ($user->isAsrama()) {
            // Jika pengurus asrama, PAKSA hanya melihat asramanya sendiri
            // dd("Lembaga User: " . $user->lembaga, "Contoh Asrama di Siswa: " . Siswa::first()->asrama);
            $query->where('asrama', $user->lembaga);
        } elseif ($request->filled('filter_asrama')) {
            // Jika admin pusat, baru boleh pakai filter dari dropdown
            $query->where('asrama', $request->filter_asrama);
        }

        // 3. FILTER KAMAR (Input dari Dropdown)
        if ($request->filled('filter_kamar')) {
            $query->where('kamar', $request->filter_kamar);
        }

        // 4. FITUR PENCARIAN: Nama atau ID Person
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('idperson', 'like', "%{$search}%");
            });
        }

        // 5. PAGINATION
        $siswas = $query->paginate(15)->withQueryString();

        if ($request->has('ajax')) {
            return response()->json([
                'html' => view('siswa._list', compact('siswas'))->render(),
                'pagination' => $siswas->links()->render()
            ]);
        }

        $listAsrama = Asrama::select('asrama')->distinct()->pluck('asrama');
        $listKamar = Asrama::select('asrama', 'kamar')->distinct()->get();

        $htmlContent = view('siswa._list', compact('siswas'))->render();
        $paginationLinks = $siswas->links()->render();

        return view('siswa.index', compact('siswas', 'listAsrama', 'listKamar', 'htmlContent', 'paginationLinks'));
    }
    /**
     * Menampilkan detail siswa & status pembayaran mendalam
     */
    public function show($idperson)
    {
        $siswa = Siswa::findOrFail($idperson);
        $user = Auth::user();

        // Validasi akses lagi untuk keamanan URL direct access
        if ($user->isAsrama() && $siswa->AsramaPondok !== $user->lembaga) {
            abort(403, 'Anda tidak memiliki akses ke data santri di asrama lain.');
        }

        // Ambil data tagihan live dari PembayaranService
        $tunggakan = $this->pembayaranService->getTotalBelumLunas($idperson);
        $detailTagihan = $this->pembayaranService->getDetailPembayaran($idperson);
        $summaryPeriode = $this->pembayaranService->getSummaryPerPeriode($idperson);

        return view('siswa.show', compact(
            'siswa',
            'tunggakan',
            'detailTagihan',
            'summaryPeriode'
        ));
    }

    /**
     * API Response untuk pengecekan cepat (jika butuh AJAX)
     */
    public function checkStatus($idperson)
    {
        $tunggakan = $this->pembayaranService->getTotalBelumLunas($idperson);

        return response()->json([
            'idperson' => $idperson,
            'boleh_boyong' => $tunggakan <= 0,
            'sisa_tagihan' => $tunggakan,
            'formatted_tagihan' => 'Rp ' . number_format($tunggakan, 0, ',', '.')
        ]);
    }
}