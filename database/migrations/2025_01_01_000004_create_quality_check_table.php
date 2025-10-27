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
        Schema::create('quality_check', function (Blueprint $table) {
            $table->id('quality_check_id'); // Primary key
            $table->string('user_id'); // Will reference user_id in users table
            $table->foreignId('barang_id')->constrained('barang', 'barang_id')->onDelete('cascade');
            $table->date('tanggal_check');
            $table->string('status'); // e.g., 'pass', 'fail'
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_check');
    }
};