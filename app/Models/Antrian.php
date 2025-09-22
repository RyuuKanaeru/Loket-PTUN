<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Antrian extends Model
{
    protected $fillable = [
        'loket_id',
        'nomor',
        'status',
    ];

    protected $casts = [
        'nomor' => 'integer',
        'status' => 'string',
    ];

    /**
     * Get the loket that owns this antrian
     */
    public function loket(): BelongsTo
    {
        return $this->belongsTo(Loket::class);
    }

    /**
     * Mark this antrian as called
     */
    public function markAsCalled(): void
    {
        $this->status = 'dipanggil';
        $this->save();
    }

    /**
     * Check if this antrian is waiting
     */
    public function isWaiting(): bool
    {
        return $this->status === 'menunggu';
    }

    /**
     * Check if this antrian has been called
     */
    public function isCalled(): bool
    {
        return $this->status === 'dipanggil';
    }
}