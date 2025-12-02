<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('size')->insert([
            ['size_label' => 'XS',  'width' => 46, 'height' => 64, 'sleeve' => 15],
            ['size_label' => 'S',   'width' => 48, 'height' => 66, 'sleeve' => 16],
            ['size_label' => 'M',   'width' => 50, 'height' => 68, 'sleeve' => 17],
            ['size_label' => 'L',   'width' => 52, 'height' => 70, 'sleeve' => 18],
            ['size_label' => 'XL',  'width' => 54, 'height' => 72, 'sleeve' => 19],
            ['size_label' => 'XXL', 'width' => 56, 'height' => 74, 'sleeve' => 20],
        ]);
    }
};
