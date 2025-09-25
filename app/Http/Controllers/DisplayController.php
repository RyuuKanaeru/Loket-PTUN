<?php

namespace App\Http\Controllers;

use App\Models\Loket;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    // Tampilkan halaman display (HTML)
    public function index()
    {
        // optional: pass initial data to blade (not required since JS akan fetch)
        $lokets = Loket::all();
        return view('display.index', compact('lokets'));
    }

    // Endpoint JSON untuk polling (dipanggil via JS)
    public function data(Request $request)
    {
        $lokets = Loket::with(['antrianCalling' => function($q) {
            $q->latest('updated_at');
        }])->get()->map(function ($loket) {
            $calling = $loket->antrianCalling->first();
            return [
                'id' => $loket->id,
                'nama' => $loket->nama,
                'nomor' => $calling ? $calling->formatted_nomor : null,
                'updated_at' => $calling ? $calling->updated_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'lokets' => $lokets,
            'timestamp' => now()->toDateTimeString(),
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }
}
