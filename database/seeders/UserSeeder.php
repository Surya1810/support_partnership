<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * ! Aktifkan hanya untuk di development saja
     */
    public function run(): void
    {
        User::create([
            'role_id' => '1',
            'position_id' => '1',
            'department_id' => '1',
            'name' => 'Administrator',
            'username' => 'Admin',
            'email' => 'hi@partnership.co.id',
            'password' => bcrypt('123'),
        ]);
        User::create([
            'role_id' => '2',
            'position_id' => '1',
            'department_id' => '1',
            'name' => 'Firkie Apriliza Ramadhani, SE, MM',
            'username' => 'Firkie',
            'email' => 'firkie@partnership.co.id',
            'password' => bcrypt('123'),
            'avatar' => 'firkie.jpg',
        ]);
        User::create([
            'role_id' => '4',
            'position_id' => '1',
            'department_id' => '8',
            'name' => 'Novia Fadhilah',
            'username' => 'Novia',
            'email' => 'novia@partnership.co.id',
            'password' => bcrypt('123'),
            'avatar' => 'novia.jpg',
        ]);
        User::create([
            'role_id' => '3',
            'position_id' => '1',
            'department_id' => '8',
            'name' => 'Annisa Yulianti',
            'username' => 'Annisa',
            'email' => 'annisa@partnership.co.id',
            'password' => bcrypt('123'),
            'avatar' => 'annisa.jpg',
        ]);
        User::create([
            'role_id' => '3',
            'position_id' => '1',
            'department_id' => '5',
            'name' => 'Rudy Haryanto',
            'username' => 'Rudy',
            'email' => 'rudy@partnership.co.id',
            'password' => bcrypt('123'),
            'avatar' => 'rudy.jpg',
        ]);
        User::create([
            'role_id' => '3',
            'position_id' => '1',
            'department_id' => '3',
            'name' => 'Uci Ahmad',
            'username' => 'Uci',
            'email' => 'uci@partnership.co.id',
            'password' => bcrypt('123'),
            'avatar' => 'uci.jpg',
        ]);
        User::create([
            'role_id' => '4',
            'position_id' => '1',
            'department_id' => '3',
            'name' => 'Encep Zainul Syah',
            'username' => 'Enza',
            'email' => 'enza@partnership.co.id',
            'password' => bcrypt('123'),
            'avatar' => 'encep.jpg',
        ]);
        User::create([
            'role_id' => '4',
            'position_id' => '1',
            'department_id' => '5',
            'name' => 'Surya Dinarta Halim',
            'username' => 'Surya',
            'email' => 'surya@partnership.co.id',
            'password' => bcrypt('123'),
            'avatar' => 'surya.jpg',
        ]);
        User::create([
            'role_id' => '5',
            'position_id' => '1',
            'department_id' => '5',
            'name' => 'Suhaefi',
            'username' => 'Suhaefi',
            'email' => 'suhaefi@partnership.co.id',
            'password' => bcrypt('123'),
            'avatar' => 'profile.jpg',
        ]);
        User::create([
            'role_id' => '5',
            'position_id' => '1',
            'department_id' => '5',
            'name' => 'Maulida',
            'username' => 'Maulida',
            'email' => 'maulida@partnership.co.id',
            'password' => bcrypt('123'),
            'avatar' => 'profile.jpg',
        ]);
        User::create([
            'role_id' => '1',
            'position_id' => '1',
            'department_id' => '5',
            'name' => 'Ibnu',
            'username' => 'Ibnu',
            'email' => 'Ibnu@partnership.co.id',
            'password' => bcrypt('123'),
            'avatar' => 'profile.jpg',
        ]);
        User::create([
            'role_id' => '4',
            'position_id' => '1',
            'department_id' => '3',
            'name' => 'Duhan Arif',
            'username' => 'Duhan',
            'email' => 'duhan@partnership.co.id',
            'password' => bcrypt('123'),
            'avatar' => 'duhan.jpg',
        ]);
        User::create([
            'role_id' => '5',
            'position_id' => '1',
            'department_id' => '3',
            'name' => 'Dani Nugraha',
            'username' => 'Dani',
            'email' => 'dani@partnership.co.id',
            'password' => bcrypt('123'),
            'avatar' => 'dani.jpg',
        ]);
    }
}
