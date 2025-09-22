<?php

namespace App\Http\Controllers;

use App\Models\Loket;
use App\Models\Antrian;
use Illuminate\Http\Request;

class LoketAdminController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index()
    {
        $lokets = Loket::with(['antrianMenunggu', 'antrianCalling'])->get();

        // Get active loket if any
        $activeLoket = session('active_loket');
        $selectedLoket = null;
        
        if ($activeLoket) {
            $selectedLoket = Loket::with([
                'antrianMenunggu',
                'antrianCalling',
                'riwayat' => function($query) {
                    $query->limit(10);
                }
            ])->find($activeLoket);
        }

        return view('loketadmin.admin', compact('lokets', 'selectedLoket'));
    }

    /**
     * Get latest data for active loket
     */
    public function getLatestData()
    {
        $activeLoket = session('active_loket');
        if (!$activeLoket) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada loket aktif'
            ]);
        }

        $loket = Loket::with([
            'antrianMenunggu',
            'antrianCalling',
            'riwayat' => function($query) {
                $query->limit(10);
            }
        ])->find($activeLoket);

        return response()->json([
            'success' => true,
            'data' => [
                'antrian_menunggu' => $loket->antrianMenunggu,
                'antrian_calling' => $loket->antrianCalling->first(),
                'riwayat' => $loket->riwayat
            ]
        ]);
    }

    /**
     * Set active loket for the admin
     */
    public function setActiveLoket($loketId)
    {
        $loket = Loket::findOrFail($loketId);
        session(['active_loket' => $loketId]);

        return redirect()->route('admin.index')->with('success', "Anda sekarang mengelola {$loket->nama}");
    }

    /**
     * Call next number for specific counter
     */
    public function callNext($loketId)
    {
        $loket = Loket::findOrFail($loketId);
        
        // Mark any previous calling number as done
        Antrian::where('loket_id', $loketId)
               ->where('status', 'calling')
               ->update(['status' => 'selesai']);

        $current = $loket->getCurrentWaitingAntrian();

        if ($current) {
            $current->status = 'calling';
            $current->save();
            
            return response()->json([
                'success' => true,
                'nomor' => $current->nomor,
                'loket' => $loket->nama
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada antrian yang menunggu'
        ]);
    }

    /**
     * Mark current number as done
     */
    public function markAsDone($loketId)
    {
        $antrian = Antrian::where('loket_id', $loketId)
                         ->where('status', 'calling')
                         ->first();

        if ($antrian) {
            $antrian->status = 'selesai';
            $antrian->save();

            return response()->json([
                'success' => true,
                'message' => "Nomor {$antrian->nomor} telah selesai"
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada antrian yang sedang dipanggil'
        ]);
    }

    /**
     * Update counter name
     */
    public function updateLoket(Request $request, $loketId)
    {
        $request->validate([
            'nama' => 'required|string|max:255'
        ]);

        $loket = Loket::findOrFail($loketId);
        $loket->update(['nama' => $request->nama]);

        return response()->json([
            'success' => true,
            'loket' => $loket
        ]);
    }
}