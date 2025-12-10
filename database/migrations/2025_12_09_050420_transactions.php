<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // jika ada FK lama, hapus dulu (aman jika belum ada)
        try {
            DB::statement('ALTER TABLE `transactions` DROP FOREIGN KEY `transactions_id_foreign`');
        } catch (\Throwable $e) {
            // ignore
        }

        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('transaction_id');
            $table->date('date')->nullable(false);

            // kolom yang merujuk ke avail_stocks.id — harus nullable agar ON DELETE SET NULL bekerja
            $table->unsignedBigInteger('id')->nullable()->index();

            $table->string('product_name')->nullable();
            $table->string('size')->nullable();
            $table->integer('qty')->default(0);
            $table->integer('price')->default(0);
            $table->integer('total')->default(0);
            $table->integer('paid')->default(0);
            $table->string('payment_method')->nullable();
            $table->integer('unpaid_amount')->default(0);
            $table->date('due_date_payment')->nullable();
            $table->string('status')->nullable();

            $table->timestamps();

            // FK ke avail_stocks.id
            $table->foreign('id')->references('id')->on('avail_stocks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // drop FK safely lalu drop table
        try {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropForeign(['id']);
            });
        } catch (\Throwable $e) {
            // ignore
        }

        Schema::dropIfExists('transactions');
    }
};
