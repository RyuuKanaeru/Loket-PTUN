<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loket extends Model
{
    protected $fillable = [
        'nama',
        'nomor_terakhir',
        'kode_prefix', // tambahin biar bisa mass assign
    ];

    protected $casts = [
        'nomor_terakhir' => 'integer',
    ];

    /**
     * Relasi: semua antrian dari loket ini
     */
    public function antrians(): HasMany
    {
        return $this->hasMany(Antrian::class);
    }

    /**
     * Relasi: antrian yang menunggu
     */
    public function antrianMenunggu(): HasMany
    {
        return $this->hasMany(Antrian::class)
                    ->where('status', 'menunggu')
                    ->orderBy('nomor');
    }

    /**
     * Relasi: antrian yang sedang dipanggil
     */
    public function antrianCalling(): HasMany
    {
        return $this->hasMany(Antrian::class)
                    ->where('status', 'calling');
    }

    /**
     * Relasi: riwayat antrian yang sudah selesai
     */
    public function riwayat(): HasMany
    {
        return $this->hasMany(Antrian::class)
                    ->where('status', 'selesai')
                    ->orderBy('updated_at', 'desc');
    }

    /**
     * Ambil antrian pertama yang sedang menunggu
     */
    public function getCurrentWaitingAntrian()
    {
        return $this->antrianMenunggu()->first();
    }

    /**
     * Ambil nomor antrian terakhir dalam format A001, B002, dll.
     */
    public function getNomorAntrianAttribute(): string
    {
        return $this->kode_prefix . str_pad($this->nomor_terakhir, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Naikkan nomor terakhir +1, lalu kembalikan dalam format A001, B002, dll.
     */
    public function getNextNumber(): string
    {
        $this->increment('nomor_terakhir');
        return $this->kode_prefix . str_pad($this->nomor_terakhir, 3, '0', STR_PAD_LEFT);
    }
}
