<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add foreign key constraints to all tables that reference user_id
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
        
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
        
        Schema::table('quality_check', function (Blueprint $table) {
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
        
        Schema::table('order_massal', function (Blueprint $table) {
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
        
        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
        });
        
        Schema::table('transaksi', function (Blueprint $table) {
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        Schema::table('order_massal', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        Schema::table('quality_check', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};