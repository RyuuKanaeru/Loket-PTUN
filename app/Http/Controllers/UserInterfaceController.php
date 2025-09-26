<?php

namespace App\Http\Controllers;

use App\Models\Loket;
use App\Models\Antrian;
use Illuminate\Http\Request;

class UserInterfaceController extends Controller
{
    /**
     * Display the user interface for creating queue numbers
     */
    public function index()
    {
        $lokets = Loket::all();
        return view('userinterface.user', compact('lokets'));
    }

    /**
     * Create a new queue number for the selected counter
     */
    public function createAntrian(Request $request)
    {
        $request->validate([
            'loket_id' => 'required|exists:lokets,id'
        ]);

        $loket = Loket::findOrFail($request->loket_id);
        $antrian = $loket->antrians()->create([
            'nomor' => $loket->getNextNumber(),
            'status' => 'menunggu'
        ]);

        return response()->json([
            'success' => true,
            'nomor' => $antrian->formatted_nomor,
            'loket' => $loket->nama,
            'message' => "Silahkan menunggu nomor antrian anda dipanggil"
        ]);
    }

    /**
     * Get current waiting numbers for all counters
     */
    public function getStatus()
    {
        $lokets = Loket::with(['antrians' => function($query) {
            $query->where('status', 'menunggu')
                  ->orderBy('nomor');
        }])->get();

        $status = $lokets->map(function($loket) {
            return [
                'id' => $loket->id,
                'nama' => $loket->nama,
                'nomor_terakhir' => $loket->nomor_terakhir,
                'jumlah_menunggu' => $loket->antrians->count()
            ];
        });

        return response()->json($status);
    }
}