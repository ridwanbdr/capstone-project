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
        Schema::create('order_massal', function (Blueprint $table) {
            $table->id('pesanan_id'); // Primary key
            $table->string('user_id'); // Will reference user_id in users table
            $table->string('nama_pemesan');
            $table->date('tanggal_pesan');
            $table->string('status_pesanan')->default('pending'); // e.g., 'pending', 'processing', 'completed'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_massal');
    }
};