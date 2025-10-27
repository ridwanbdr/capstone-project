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
        // Add the required columns for our application without unique constraint first
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_id')->nullable()->after('id'); // Add user_id field as nullable first
            $table->string('username')->nullable()->after('email');
            $table->string('role')->default('user')->after('username');
            $table->string('nama_lengkap')->after('role');
        });
        
        // Update existing records to have user_id based on the auto-incrementing id
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'user_id' => 'USR' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                    'nama_lengkap' => $user->name ?? $user->email,
                    'username' => $user->email,
                ]);
        }
        
        // Now make user_id unique
        Schema::table('users', function (Blueprint $table) {
            $table->unique('user_id');
        });
        
        // Optionally, drop the original 'name' column if not needed
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('email');
            $table->dropUnique(['user_id']); // Remove unique constraint first
            $table->dropColumn(['user_id', 'username', 'role', 'nama_lengkap']);
        });
        
        // Restore names from nama_lengkap if needed
        DB::table('users')->whereNotNull('nama_lengkap')->update([
            'name' => DB::raw('nama_lengkap')
        ]);
    }
};