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
        if (getenv('APP_ENV') == 'local') {
            /**
             * Yang tidak memiliki code
             * maka tidak aktif
             */
            Department::create([
                'name' => 'Partnership Procurement Solution',
                'code' => '02',
            ]);
            Department::create([
                'name' => 'Partnership Workshop',
                'code' => null,
            ]);
            Department::create([
                'name' => 'Partnership Contruction',
                'code' => '03'
            ]);
            Department::create([
                'name' => 'Partnership Law Consultant',
                'code' => null
            ]);
            Department::create([
                'name' => 'Partnership Technology',
                'code' => '04'
            ]);
            Department::create([
                'name' => 'Partnership Information System & Website',
                'code' => null
            ]);
            Department::create([
                'name' => 'Partnership Event Organizer & Mice',
                'code' => null
            ]);
            Department::create([
                'name' => 'Parnership Finance',
                'code' => null
            ]);
            Department::create([
                'name' => 'Partnership General Affair',
                'code' => '01',
            ]);
        }
    }
}
