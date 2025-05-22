<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\CostCenter;

class CostCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (env('APP_ENV') === 'local') {
            // Procurement
            CostCenter::create([
                'department_id' => 1,
                'name' => 'Cost Center Procurement'
            ]);

            // Construction
            CostCenter::create([
                'department_id' => 3,
                'name' => 'Cost Center Konstruksi',
            ]);

            // Teknologi
            CostCenter::create([
                'department_id' => 5,
                'name' => 'Cost Center Teknologi'
            ]);

            // Rumah Tangga
            CostCenter::create([
                'department_id' => 9,
                'name' => 'Cost Center Rumah Tangga'
            ]);
        }
    }
}
