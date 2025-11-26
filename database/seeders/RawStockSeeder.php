<?php

namespace Database\Seeders;

use App\Models\RawStock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RawStockSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rawStocks = [
            [
                'material_name' => 'Kain Katun Premium',
                'material_qty' => 100,
                'satuan' => 'meter',
                'category' => 'Kain Utama',
                'unit_price' => 50000,
                'total_price' => 5000000, // 100 * 50000
                'added_on' => Carbon::now()->subDays(30)->format('Y-m-d'),
            ],
            [
                'material_name' => 'Benang Polyester',
                'material_qty' => 50,
                'satuan' => 'roll',
                'category' => 'Benang',
                'unit_price' => 75000,
                'total_price' => 3750000, // 50 * 75000
                'added_on' => Carbon::now()->subDays(20)->format('Y-m-d'),
            ],
            [
                'material_name' => 'Kancing Baju',
                'material_qty' => 200,
                'satuan' => 'pcs',
                'category' => 'Aksesoris',
                'unit_price' => 500,
                'total_price' => 100000, // 200 * 500
                'added_on' => Carbon::now()->subDays(10)->format('Y-m-d'),
            ],
        ];

        foreach ($rawStocks as $stock) {
            RawStock::create($stock);
        }
    }
}
