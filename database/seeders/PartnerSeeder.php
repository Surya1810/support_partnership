<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partner = Partner::create([
            'name' => 'IPI Packers Australia',
            'contact' => 'Pak Hikmat Nugraha',
            'number' => '601112524065',
            'keyword' => 'geothermal,PSDMBP',
            'desc' => 'Sales Asia Tenggara',
        ]);
        $partner = Partner::create([
            'name' => 'Partnership Jepara',
            'contact' => 'Pak Djambrong',
            'number' => '628122997364',
            'keyword' => 'interior, workshop',
            'desc' => 'Workshop Partnership Jepara',
        ]);
    }
}
