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
        Schema::create('production_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_id');
            $table->unsignedBigInteger('material_id');
            $table->integer('material_qty');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('production_id')
                  ->references('production_id')
                  ->on('productions')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
                  
            $table->foreign('material_id')
                  ->references('material_id')
                  ->on('raw_stocks')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
            
            // Unique constraint untuk mencegah duplikasi material dalam satu production
            $table->unique(['production_id', 'material_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_materials');
    }
};
