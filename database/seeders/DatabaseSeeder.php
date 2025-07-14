<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (getenv('APP_ENV') == 'local') {
            $this->call(RoleSeeder::class);
            $this->call(DepartmentSeeder::class);
            $this->call(UserSeeder::class);
            $this->call(UserExtensionSeeder::class);
            $this->call(CostCenterCategoriesSeeder::class);
            $this->call(ClientSeeder::class);
            $this->call(SupplierSeeder::class);
        }
    }
}
