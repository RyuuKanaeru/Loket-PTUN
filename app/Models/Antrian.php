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

    protected $appends = ['formatted_nomor'];

    public function loket()
    {
        return $this->belongsTo(Loket::class);
    }

    /**
     * Accessor: format nomor jadi A001, B002, dst.
     */
    public function getFormattedNomorAttribute()
    {
        return $this->loket->kode_prefix . str_pad($this->nomor, 3, '0', STR_PAD_LEFT);
    }
}
