<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CostCenterCategory;

class CostCenterCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (env('APP_ENV') == 'local') {
            CostCenterCategory::insert([
                [
                    'code' => 'KS',
                    'name' => 'Kas/Pemasukan',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'code' => 'BF',
                    'name' => 'Belanja Follow Up',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'code' => 'BO',
                    'name' => 'Belanja Overhead',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'code' => 'BG',
                    'name' => 'Belanja Gaji',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'code' => 'BA',
                    'name' => 'Belanja Aset',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'code' => 'BP',
                    'name' => 'Belanja Project',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'code' => 'BH',
                    'name' => 'Biaya Hutang',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'code' => 'PP',
                    'name' => 'Pemasukan Piutang',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'code' => 'PR',
                    'name' => 'Pemeliharaan Rutin',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'code' => 'BR',
                    'name' => 'Belanja Rembes',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }
    }
}
