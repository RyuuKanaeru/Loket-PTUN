<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loket extends Model
{
    protected $fillable = [
        'nama',
        'nomor_terakhir',
    ];

    protected $casts = [
        'nomor_terakhir' => 'integer',
    ];

    /**
     * Get all antrian for this loket
     */
    public function antrians(): HasMany
    {
        return $this->hasMany(Antrian::class);
    }

    /**
     * Increment and get the next queue number
     */
    public function getNextNumber(): int
    {
        $this->increment('nomor_terakhir');
        return $this->nomor_terakhir;
    }

    /**
     * Get current waiting antrian
     */
    public function getCurrentWaitingAntrian()
    {
        return $this->antrians()
            ->where('status', 'menunggu')
            ->orderBy('nomor')
            ->first();
    }
}
