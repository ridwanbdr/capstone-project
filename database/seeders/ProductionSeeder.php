<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Production;
use App\Models\RawStock;
use Carbon\Carbon;
use DB;

class ProductionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // contoh data produksi dengan daftar bahan (nama bahan dan qty yang diinginkan)
        $productions = [
            [
                'production_lead' => 'Tim A',
                'production_label' => 'Batch A1',
                'production_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'total_unit' => 50,
                'materials' => [
                    ['name' => 'Kain Katun Premium', 'desired_qty' => 20],
                    ['name' => 'Benang Polyester', 'desired_qty' => 5],
                ],
            ],
            [
                'production_lead' => 'Tim B',
                'production_label' => 'Batch B1',
                'production_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'total_unit' => 30,
                'materials' => [
                    ['name' => 'Kain Katun Premium', 'desired_qty' => 40],
                    ['name' => 'Kancing Baju', 'desired_qty' => 50],
                ],
            ],
            [
                'production_lead' => 'Tim C',
                'production_label' => 'Batch C1',
                'production_date' => Carbon::now()->format('Y-m-d'),
                'total_unit' => 20,
                'materials' => [
                    ['name' => 'Benang Polyester', 'desired_qty' => 10],
                ],
            ],
        ];

        DB::transaction(function () use ($productions) {
            foreach ($productions as $p) {
                $prod = Production::create([
                    'production_lead' => $p['production_lead'],
                    'production_label' => $p['production_label'],
                    'production_date' => $p['production_date'],
                    'total_unit' => $p['total_unit'],
                ]);

                // attach materials ensuring qty tidak melebihi stok yang ada
                foreach ($p['materials'] as $mat) {
                    $raw = RawStock::where('material_name', $mat['name'])->first();

                    if (!$raw) {
                        // jika material tidak ditemukan, skip
                        continue;
                    }

                    // pastikan qty yang dipakai tidak melebihi stok
                    $attachQty = min((int) $mat['desired_qty'], (int) $raw->material_qty);

                    if ($attachQty <= 0) {
                        // skip jika stok nol atau desired nol
                        continue;
                    }

                    // attach ke pivot production_materials (material_qty)
                    $prod->rawStocks()->attach($raw->material_id, [
                        'material_qty' => $attachQty,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });
    }
}