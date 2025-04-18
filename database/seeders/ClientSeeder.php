<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = Client::create([
            'name' => 'BJB Cabang Patrol',
            'contact' => 'Pak Ihsan',
            'number' => '6285310001999',
            'position' => 'Sekretaris Umum',
        ]);
        $user = Client::create([
            'name' => 'Dinas Pendidikan Kabupaten Pangandaran',
            'contact' => 'Ibu Icha',
            'number' => '628121446986',
            'position' => 'Pengawas Mutu',
        ]);
    }
}
