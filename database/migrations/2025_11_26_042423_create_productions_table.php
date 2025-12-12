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
        Schema::create('productions', function (Blueprint $table) {
            $table->id('production_id');
            $table->string('production_lead');
            $table->string('production_label');
            $table->date('production_date');
            $table->unsignedBigInteger('material_id'); // Foreign key ke raw_stocks
            $table->integer('material_qty');
            $table->integer('total_unit');
            $table->timestamps();
            
            // Menambahkan foreign key constraint
            $table->foreign('material_id')
                  ->references('material_id')
                  ->on('raw_stocks')
                  ->onDelete('restrict') // atau 'cascade' jika ingin hapus production saat raw_stock dihapus
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
