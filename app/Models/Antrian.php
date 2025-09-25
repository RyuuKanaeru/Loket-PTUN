<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    use HasFactory;

    protected $fillable = [
        'loket_id',
        'nomor',
        'status',
    ];

    public function loket()
    {
        return $this->belongsTo(Loket::class);
    }

    /**
     * Accessor: format nomor jadi A001, B002, dst.
     */
    public function getFormattedNomorAttribute()
    {
        // Mapping prefix berdasarkan ID loket
        $prefixMap = [
            1 => 'A',
            2 => 'B',
            3 => 'C',
            4 => 'D',
            5 => 'E',
        ];

        $prefix = $prefixMap[$this->loket_id] ?? 'X';
        return $prefix . str_pad($this->nomor, 3, '0', STR_PAD_LEFT);
    }
}
