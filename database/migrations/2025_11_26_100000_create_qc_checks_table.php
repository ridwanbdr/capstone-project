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
            $table->id();
            $table->string('qc_id')->unique();
            // $table->string('product_name');
            $table->integer('qty_passed')->default(0);
            $table->integer('qty_reject')->default(0);
            $table->date('date');
            $table->string('qc_checker')->nullable();
            $table->string('qc_label')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('qc_checker')
                ->references('user_id')
                ->on('users')
                ->onDelete('set null');

            // $table->foreign('product_name')
            //     ->references('product_name')
            //     ->on('productions')
            //     ->onDelete('cascade');
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
