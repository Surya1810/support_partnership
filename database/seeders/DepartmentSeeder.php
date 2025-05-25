<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (env('APP_ENV') === 'local') {
            Department::create([
                'name' => 'Partnership Procurement Solution'
            ]);
            // Department::create([
            //     'name' => 'Partnership Workshop'
            // ]);
            Department::create([
                'name' => 'Partnership Contractor'
            ]);
            // Department::create([
            //     'name' => 'Partnership Law Consultant'
            // ]);
            Department::create([
                'name' => 'Partnership Technology'
            ]);
            // Department::create([
            //     'name' => 'Partnership Information System & Website'
            // ]);
            // Department::create([
            //     'name' => 'Partnership Event Organizer & Mice'
            // ]);
            // Department::create([
            //     'name' => 'Finance'
            // ]);
            Department::create([
                'name' => 'Rumah Tangga'
            ]);
        }
    }
}
