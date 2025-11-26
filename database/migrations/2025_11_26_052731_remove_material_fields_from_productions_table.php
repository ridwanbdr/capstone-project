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
        if (Schema::hasTable('productions')) {
            Schema::table('productions', function (Blueprint $table) {
                // Hapus foreign key constraint terlebih dahulu
                if (Schema::hasColumn('productions', 'material_id')) {
                    $table->dropForeign(['material_id']);
                }
                
                // Hapus kolom material_id dan material_qty
                if (Schema::hasColumn('productions', 'material_id')) {
                    $table->dropColumn('material_id');
                }
                
                if (Schema::hasColumn('productions', 'material_qty')) {
                    $table->dropColumn('material_qty');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('productions')) {
            Schema::table('productions', function (Blueprint $table) {
                // Kembalikan kolom jika rollback
                $table->unsignedBigInteger('material_id')->nullable()->after('production_date');
                $table->integer('material_qty')->nullable()->after('material_id');
                
                // Tambahkan foreign key kembali
                $table->foreign('material_id')
                      ->references('material_id')
                      ->on('raw_stocks')
                      ->onDelete('restrict')
                      ->onUpdate('cascade');
            });
        }
    }
};
