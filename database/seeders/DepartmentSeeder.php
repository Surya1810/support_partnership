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
            /**
             * Yang memiliki code di sini artinya yang aktif
             * Jangan hapus, nanti error relasinya
             */
            Department::create([
                'name' => 'Partnership Procurement Solution',
                'code' => '01'
            ]);
            Department::create([
                'name' => 'Partnership Workshop',
            ]);
            Department::create([
                'name' => 'Partnership Contractor',
                'code' => '03'
            ]);
            Department::create([
                'name' => 'Partnership Law Consultant'
            ]);
            Department::create([
                'name' => 'Partnership Technology',
                'code' => '02'
            ]);
            Department::create([
                'name' => 'Partnership Information System & Website'
            ]);
            Department::create([
                'name' => 'Partnership Event Organizer & Mice'
            ]);
            Department::create([
                'name' => 'Finance'
            ]);
            Department::create([
                'name' => 'Rumah Tangga',
                'code' => '00'
            ]);
        }
    }
}
