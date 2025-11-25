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
        Schema::create('raw_stocks', function (Blueprint $table) {
            $table->id('material_id');
            $table->string('material_name');
            $table->integer('material_qty');
            $table->integer('unit_price');
            $table->integer('total_price');
            $table->date('added_on');
            $table->timestamps(); // opsional, bisa dihapus kalau tidak pakai created_at/updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_stocks');
    }
};
