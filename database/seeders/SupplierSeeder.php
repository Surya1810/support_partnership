<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supplier = Supplier::create([
            'name' => 'Asahi',
            'contact' => 'Pak Yudhi',
            'number' => '628118204632',
            'keyword' => 'geothermal,PSDMBP',
            'keterangan' => 'Sales Asahi',
        ]);
    }
}
