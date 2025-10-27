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
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id('detail_id'); // Primary key
            $table->foreignId('pesanan_id')->constrained('order_massal', 'pesanan_id')->onDelete('cascade');
            $table->string('user_id')->nullable(); // Will reference user_id in users table
            $table->string('nama_produk');
            $table->string('jenis_kain');
            $table->string('warna');
            $table->string('ukuran');
            $table->integer('jumlah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
    }
};