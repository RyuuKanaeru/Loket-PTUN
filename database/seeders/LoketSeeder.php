<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Loket;

class LoketSeeder extends Seeder
{
    public function run(): void
    {
        $defaultLokets = [
            'Loket 1',
            'Loket 2',
            'Loket 3',
            'Loket 4',
            'Loket 5',
        ];

        foreach ($defaultLokets as $nama) {
            Loket::updateOrCreate(
                ['nama' => $nama], // cek berdasarkan nama
                ['nomor_terakhir' => 0] // kalau ada, reset nomor
            );
        }
    }
};