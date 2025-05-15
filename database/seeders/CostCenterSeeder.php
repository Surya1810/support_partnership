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
            'name' => 'Kas'
        ]);
        CostCenter::create([
            'department_id' => 5,
            'name' => 'Project'
        ]);
        CostCenter::create([
            'department_id' => 5,
            'name' => 'Gaji'
        ]);
        CostCenter::create([
            'department_id' => 5,
            'name' => 'Kebutuhan Rumah Tangga'
        ]);

        // Procurement
        CostCenter::create([
            'department_id' => 1,
            'name' => 'Kas'
        ]);
        CostCenter::create([
            'department_id' => 1,
            'name' => 'Project'
        ]);
        CostCenter::create([
            'department_id' => 1,
            'name' => 'Gaji'
        ]);
        CostCenter::create([
            'department_id' => 1,
            'name' => 'Kebutuhan Rumah Tangga'
        ]);

        // Procurement
        CostCenter::create([
            'department_id' => 3,
            'name' => 'Kas'
        ]);
        CostCenter::create([
            'department_id' => 3,
            'name' => 'Project'
        ]);
        CostCenter::create([
            'department_id' => 3,
            'name' => 'Gaji'
        ]);
        CostCenter::create([
            'department_id' => 3,
            'name' => 'Kebutuhan Rumah Tangga'
        ]);
    }
}
