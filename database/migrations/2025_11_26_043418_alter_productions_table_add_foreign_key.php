<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migration ini untuk mengubah kolom material_name menjadi material_id (foreign key)
     * Hanya dijalankan jika tabel sudah ada dengan struktur lama
     */
    public function up(): void
    {
        if (Schema::hasTable('productions')) {
            Schema::table('productions', function (Blueprint $table) {
                // Cek apakah kolom material_name ada (struktur lama)
                if (Schema::hasColumn('productions', 'material_name')) {
                    // Hapus kolom material_name jika ada
                    $table->dropColumn('material_name');
                }
                
                // Cek apakah kolom material_id belum ada
                if (!Schema::hasColumn('productions', 'material_id')) {
                    // Tambahkan kolom material_id
                    $table->unsignedBigInteger('material_id')->after('production_date');
                    
                    // Tambahkan foreign key constraint
                    $table->foreign('material_id')
                          ->references('material_id')
                          ->on('raw_stocks')
                          ->onDelete('restrict')
                          ->onUpdate('cascade');
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
                // Hapus foreign key constraint
                $table->dropForeign(['material_id']);
                
                // Hapus kolom material_id
                $table->dropColumn('material_id');
                
                // Kembalikan kolom material_name (jika diperlukan untuk rollback)
                $table->string('material_name')->after('production_date');
            });
        }
    }
};
