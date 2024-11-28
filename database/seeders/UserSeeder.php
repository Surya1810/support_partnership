<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'role_id' => '1',
            'position_id' => '1',
            'department_id' => '1',
            'name' => 'Administrator',
            'username' => 'Admin',
            'email' => 'hi@partnership.co.id',
            'password' => bcrypt('123'),
        ]);
        $user = User::create([
            'role_id' => '2',
            'position_id' => '1',
            'department_id' => '1',
            'name' => 'Firkie Apriliza Ramadhani, SE, MM',
            'username' => 'Firkie',
            'email' => 'firkie@partnership.co.id',
            'password' => bcrypt('123'),
        ]);
        $user = User::create([
            'role_id' => '4',
            'position_id' => '1',
            'department_id' => '1',
            'name' => 'Encep Zainul Syah',
            'username' => 'Enza',
            'email' => 'enza@partnership.co.id',
            'password' => bcrypt('123'),
        ]);
        $user = User::create([
            'role_id' => '1',
            'position_id' => '1',
            'department_id' => '1',
            'name' => 'Surya Dinarta Halim',
            'username' => 'Surya',
            'email' => 'surya@partnership.co.id',
            'password' => bcrypt('123'),
        ]);
    }
}
