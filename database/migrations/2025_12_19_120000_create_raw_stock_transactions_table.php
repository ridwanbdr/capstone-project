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
        Schema::create('raw_stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id')->nullable()->index();
            $table->string('material_name');
            $table->integer('qty');
            $table->string('satuan')->nullable();
            $table->integer('unit_price')->default(0);
            $table->integer('total_price')->default(0);
            $table->date('added_on');
            $table->timestamps();

            $table->foreign('material_id')
                  ->references('material_id')
                  ->on('raw_stocks')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('raw_stock_transactions', function (Blueprint $table) {
                $table->dropForeign(['material_id']);
            });
        } catch (\Throwable $e) {
            // ignore
        }

        Schema::dropIfExists('raw_stock_transactions');
    }
};
