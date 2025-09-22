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
     * Get antrian that are waiting
     */
    public function antrianMenunggu(): HasMany
    {
        return $this->hasMany(Antrian::class)
                    ->where('status', 'menunggu')
                    ->orderBy('nomor');
    }

    /**
     * Get antrian that are currently being called
     */
    public function antrianCalling(): HasMany
    {
        return $this->hasMany(Antrian::class)
                    ->where('status', 'calling');
    }

    /**
     * Get completed antrian history
     */
    public function riwayat(): HasMany
    {
        return $this->hasMany(Antrian::class)
                    ->where('status', 'selesai')
                    ->orderBy('updated_at', 'desc');
    }

    /**
     * Get current waiting antrian
     */
    public function getCurrentWaitingAntrian()
    {
        return $this->antrianMenunggu()->first();
    }

    /**
     * Increment and get the next queue number
     */
    public function getNextNumber(): int
    {
        $this->increment('nomor_terakhir');
        return $this->nomor_terakhir;
    }
}
