<?php

namespace App\Http\Controllers;

use App\Models\Loket;
use App\Models\Antrian;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OperatorController extends Controller
{
    // ✅ Halaman dashboard operator (lihat semua loket dan statistik)
    public function dashboard()
    {
        $lokets = Loket::withCount([
            'antrians as total_masuk',
            'antrians as total_selesai' => function ($q) {
                $q->where('status', 'selesai');
            },
            'antrians as total_menunggu' => function ($q) {
                $q->where('status', 'menunggu');
            }
        ])->get();

        return view('operator.dashboard', compact('lokets'));
    }

    // ✅ Detail per loket beserta riwayat antrian
    public function detail($id)
    {
        $loket = Loket::with([
            'antrians' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        $statistik = [
            'total_masuk' => $loket->antrians()->count(),
            'total_selesai' => $loket->antrians()->where('status', 'selesai')->count(),
            'total_menunggu' => $loket->antrians()->where('status', 'menunggu')->count(),
        ];

        return view('operator.detail', compact('loket', 'statistik'));
    }

    // ✅ Backup seluruh data ke PDF
    public function backupPDF()
    {
        $lokets = Loket::with('antrians')->get();

        $pdf = Pdf::loadView('operator.backup-pdf', compact('lokets'))
                  ->setPaper('A4', 'portrait');

        return $pdf->download('backup-antrian-' . date('Ymd_His') . '.pdf');
    }

    // ✅ Reset semua data antrian & nomor loket
    public function resetAll()
    {
        // Hapus semua data antrian
        Antrian::truncate();

        // Reset nomor_terakhir tiap loket
        Loket::query()->update(['nomor_terakhir' => 0]);

        return redirect()->route('operator.dashboard')
            ->with('success', 'Semua data antrian berhasil direset.');
    }
}
