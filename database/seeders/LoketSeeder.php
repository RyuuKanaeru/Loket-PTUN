<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Loket;

class LoketSeeder extends Seeder
{
    public function run(): void
    {
        $defaultLokets = [
            ['nama' => 'Loket 1', 'prefix' => 'A'],
            ['nama' => 'Loket 2', 'prefix' => 'B'],
            ['nama' => 'Loket 3', 'prefix' => 'C'],
            ['nama' => 'Loket 4', 'prefix' => 'D'],
            ['nama' => 'Loket 5', 'prefix' => 'E'],
        ];

        foreach ($defaultLokets as $loket) {
            Loket::updateOrCreate(
                ['nama' => $loket['nama']],
                [
                    'nomor_terakhir' => 0,
                    'kode_prefix' => $loket['prefix']
                ]
            );
        }
    }
}
