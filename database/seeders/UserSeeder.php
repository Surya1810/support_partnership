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
        // $user = User::create([
        //     'role_id' => '1',
        //     'position_id' => '1',
        //     'department_id' => '1',
        //     'name' => 'Administrator',
        //     'username' => 'Admin',
        //     'email' => 'hi@partnership.co.id',
        //     'password' => bcrypt('123'),
        // ]);
        // $user = User::create([
        //     'role_id' => '2',
        //     'position_id' => '1',
        //     'department_id' => '1',
        //     'name' => 'Firkie Apriliza Ramadhani, SE, MM',
        //     'username' => 'Firkie',
        //     'email' => 'firkie@partnership.co.id',
        //     'password' => bcrypt('123'),
        //     'avatar' => 'firkie.jpg',
        // ]);
        // $user = User::create([
        //     'role_id' => '4',
        //     'position_id' => '1',
        //     'department_id' => '8',
        //     'name' => 'Novia Fadhilah',
        //     'username' => 'Novia',
        //     'email' => 'novia@partnership.co.id',
        //     'password' => bcrypt('123'),
        //     'avatar' => 'novia.jpg',
        // ]);
        // $user = User::create([
        //     'role_id' => '3',
        //     'position_id' => '1',
        //     'department_id' => '8',
        //     'name' => 'Annisa Yulianti',
        //     'username' => 'Annisa',
        //     'email' => 'annisa@partnership.co.id',
        //     'password' => bcrypt('123'),
        //     'avatar' => 'annisa.jpg',
        // ]);
        // $user = User::create([
        //     'role_id' => '3',
        //     'position_id' => '1',
        //     'department_id' => '5',
        //     'name' => 'Rudy Haryanto',
        //     'username' => 'Rudy',
        //     'email' => 'rudy@partnership.co.id',
        //     'password' => bcrypt('123'),
        //     'avatar' => 'rudy.jpg',
        // ]);
        // $user = User::create([
        //     'role_id' => '3',
        //     'position_id' => '1',
        //     'department_id' => '3',
        //     'name' => 'Uci Ahmad',
        //     'username' => 'Uci',
        //     'email' => 'uci@partnership.co.id',
        //     'password' => bcrypt('123'),
        //     'avatar' => 'uci.jpg',
        // ]);
        // $user = User::create([
        //     'role_id' => '4',
        //     'position_id' => '1',
        //     'department_id' => '3',
        //     'name' => 'Encep Zainul Syah',
        //     'username' => 'Enza',
        //     'email' => 'enza@partnership.co.id',
        //     'password' => bcrypt('123'),
        //     'avatar' => 'encep.jpg',
        // ]);
        // $user = User::create([
        //     'role_id' => '4',
        //     'position_id' => '1',
        //     'department_id' => '5',
        //     'name' => 'Surya Dinarta Halim',
        //     'username' => 'Surya',
        //     'email' => 'surya@partnership.co.id',
        //     'password' => bcrypt('123'),
        //     'avatar' => 'surya.jpg',
        // ]);
        $user = User::create([
            'role_id' => '5',
            'position_id' => '1',
            'department_id' => '5',
            'name' => 'Suhaefi',
            'username' => 'Suhaefi',
            'email' => 'Suhaefi@partnership.co.id',
            'password' => bcrypt('123'),
            'avatar' => 'profile.jpg',
        ]);
        $user = User::create([
            'role_id' => '1',
            'position_id' => '1',
            'department_id' => '5',
            'name' => 'Ibnu',
            'username' => 'Ibnu',
            'email' => 'Ibnu@partnership.co.id',
            'password' => bcrypt('123'),
            'avatar' => 'profile.jpg',
        ]);
        // $user = User::create([
        //     'role_id' => '4',
        //     'position_id' => '1',
        //     'department_id' => '3',
        //     'name' => 'Duhan Arif',
        //     'username' => 'Duhan',
        //     'email' => 'duhan@partnership.co.id',
        //     'password' => bcrypt('123'),
        //     'avatar' => 'duhan.jpg',
        // ]);
        // $user = User::create([
        //     'role_id' => '5',
        //     'position_id' => '1',
        //     'department_id' => '3',
        //     'name' => 'Dani Nugraha',
        //     'username' => 'Dani',
        //     'email' => 'dani@partnership.co.id',
        //     'password' => bcrypt('123'),
        //     'avatar' => 'dani.jpg',
        // ]);
    }
}
