<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Production;
use App\Models\DetailProduct;
use App\Models\Size;
use DB;

class DetailProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua production yang sudah ada (di-seed oleh ProductionSeeder)
        $productions = Production::all();

        DB::transaction(function () use ($productions) {
            foreach ($productions as $production) {
                // jika production tidak punya total_unit atau total_unit <= 0, skip
                $maxUnits = (int) $production->total_unit;
                if ($maxUnits <= 0) {
                    continue;
                }

                // ambil beberapa ukuran acak yang tersedia
                $sizes = Size::orderBy('size_id')->get();
                if ($sizes->isEmpty()) {
                    // tidak ada ukuran, skip pembuatan detail product untuk production ini
                    continue;
                }

                // rencanakan membuat 1..4 detail product untuk tiap production
                $itemsToCreate = rand(1, 4);
                $remaining = $maxUnits;

                for ($i = 0; $i < $itemsToCreate && $remaining > 0; $i++) {
                    // desired qty: antara 1 sampai remaining (bila banyak item, batasi supaya ada kemungkinan terbagi)
                    $maxDesired = max(1, (int) ceil($remaining / ($itemsToCreate - $i)));
                    $desired = rand(1, $maxDesired);

                    // pastikan tidak melebihi remaining
                    $qty = min($desired, $remaining);

                    // pilih size acak
                    $size = $sizes->random();

                    // generate product name unik-ish
                    $productName = sprintf('%s - Item %d', $production->production_label ?? 'Prod'.$production->production_id, $i + 1);

                    // create detail product
                    DetailProduct::create([
                        'production_id' => $production->production_id,
                        'product_name'  => $productName,
                        'size_id'       => $size->size_id,
                        'qty_unit'      => $qty,
                        'price_unit'    => 0,
                    ]);

                    $remaining -= $qty;
                }

                // NOTE: seeder memastikan total qty_unit (penjumlahan) untuk tiap production
                // tidak melebihi production->total_unit karena kita mengalokasikan dari $remaining.
            }
        });
    }
}
