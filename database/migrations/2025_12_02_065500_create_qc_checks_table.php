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
        Schema::create('qc_checks', function (Blueprint $table) {
            $table->id('qc_id');
            $table->unsignedInteger('product_id');
            $table->integer('qty_passed')->default(0);
            $table->integer('qty_reject')->default(0);
            $table->date('date');
            $table->string('qc_checker')->nullable();
            $table->string('qc_label')->nullable();
            $table->text('reject_reason')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('product_id')
                ->references('product_id')
                ->on('detail_product')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qc_checks');
    }
};
