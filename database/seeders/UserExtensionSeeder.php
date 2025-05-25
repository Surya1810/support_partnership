<?php

namespace Database\Seeders;

use App\Models\UserExtension;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserExtensionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (getenv('APP_ENV') === 'local') {
            for ($i = 1; $i <= 14; $i++) {
                UserExtension::create([
                    'user_id' => 1,
                    'nik' => 0,
                    'npwp' => 0,
                    'phone' => 0,
                    'address' => 'Jl. Golf Bar III',
                    'religion' => 'Islam',
                    'gender' => 'male',
                    'pob' => '-',
                    'dob' => '2025-05-22',
                    'hobby' => '-',
                    'disease' => '-',
                    'marriage' => 'not married yet',
                    'language' => 'passive',
                    'elementary' => 'SD',
                    'junior_high' => 'SMP',
                    'senior_high' => 'SMA',
                    'college' => 'UNIVERSITAS Z',
                    'bank' => 'ABC',
                    'account' => 1234567890
                ]);
            }
        }
    }
}
