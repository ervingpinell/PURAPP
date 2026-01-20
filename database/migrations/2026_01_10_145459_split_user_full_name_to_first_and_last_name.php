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
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->after('user_id')->nullable();
            $table->string('last_name')->after('first_name')->nullable();
        });

        // Migrate existing data
        $users = \DB::table('users')->get();
        foreach ($users as $user) {
            $parts = explode(' ', $user->full_name, 2);
            $first_name = $parts[0] ?? '';
            $last_name = $parts[1] ?? '';

            // Should update directly to avoid potential model events or validation if applicable, 
            // but standard save is usually fine here. Using DB update for safety against model changes.
            \DB::table('users')->where('user_id', $user->user_id)->update([
                'first_name' => $first_name,
                'last_name' => $last_name
            ]);
        }


        // Remove nullable constraint if desired, but for now we keep them nullable or make them non-nullable
        // Let's make them non-nullable now that we populated them
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
            $table->dropColumn('full_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name')->after('user_id')->nullable();
        });

        // Restore full_name
        $users = \DB::table('users')->get();
        foreach ($users as $user) {
            $full_name = trim($user->first_name . ' ' . $user->last_name);
            \DB::table('users')->where('user_id', $user->user_id)->update([
                'full_name' => $full_name
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name')->nullable(false)->change();
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};
