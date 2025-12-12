<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_product', function (Blueprint $table) {
            $table->increments('product_id');
            $table->unsignedInteger('production_id');
            // $table->string('production_label');
            $table->string('product_name');
            $table->unsignedInteger('size_id');
            // $table->string('size_label');
            $table->integer('qty_unit')->default(0);
            $table->integer('price_unit')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_product');
    }
};
