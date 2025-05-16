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
        // Teknologi
        CostCenter::create([
            'department_id' => 5,
            'name' => 'Project',
            'code' => '03-25-1'
        ]);
        CostCenter::create([
            'department_id' => 5,
            'name' => 'Gaji',
            'code' => '03-25-2'
        ]);
        CostCenter::create([
            'department_id' => 5,
            'name' => 'Rumah Tangga',
            'code' => '03-25-3'
        ]);

        // Procurement
        CostCenter::create([
            'department_id' => 1,
            'name' => 'Project',
            'code' => '01-25-1'
        ]);
        CostCenter::create([
            'department_id' => 1,
            'name' => 'Gaji',
            'code' => '01-25-2'
        ]);
        CostCenter::create([
            'department_id' => 1,
            'name' => 'Rumah Tangga',
            'code' => '01-25-3'
        ]);

        // Construction
        CostCenter::create([
            'department_id' => 3,
            'name' => 'Project',
            'code' => '02-25-1'
        ]);
        CostCenter::create([
            'department_id' => 3,
            'name' => 'Gaji',
            'code' => '02-25-2'
        ]);
        CostCenter::create([
            'department_id' => 3,
            'name' => 'Rumah Tangga',
            'code' => '02-25-3'
        ]);
    }
}
