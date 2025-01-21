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
        $role = Department::create([
            'name' => 'Partnership Procurement Solution'
        ]);
        $Department = Department::create([
            'name' => 'Partnership Workshop'
        ]);
        $Department = Department::create([
            'name' => 'Partnership Contractor'
        ]);
        $Department = Department::create([
            'name' => 'Partnership Law Consultant'
        ]);
        $Department = Department::create([
            'name' => 'Partnership Technology'
        ]);
        $Department = Department::create([
            'name' => 'Partnership Information System & Website'
        ]);
        $Department = Department::create([
            'name' => 'Partnership Event Organizer & Mice'
        ]);
        $Department = Department::create([
            'name' => 'Finance'
        ]);
    }
}
