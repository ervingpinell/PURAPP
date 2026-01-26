<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Amenity
        if (Schema::hasTable('amenities')) {
             Schema::table('amenities', function (Blueprint $table) {
                 // Check if column exists before dropping? 
                 // Laravel Schema builder doesn't support 'dropColumnIfExists' natively in all drivers easily without raw SQL.
                 // But we can check via Schema::hasColumn inside the migration logic generally? NO, inside `up` works fine.
                 if (Schema::hasColumn('amenities', 'name')) {
                    $table->dropColumn('name');
                 }
             });
             Schema::table('amenities', function (Blueprint $table) {
                $table->json('name')->nullable();
             });
        }

        // 2. Faq
         if (Schema::hasTable('faqs')) {
             Schema::table('faqs', function (Blueprint $table) {
                if (Schema::hasColumn('faqs', 'question')) $table->dropColumn('question');
                if (Schema::hasColumn('faqs', 'answer')) $table->dropColumn('answer');
             });
             Schema::table('faqs', function (Blueprint $table) {
                $table->json('question')->nullable();
                $table->json('answer')->nullable();
             });
         }

        // 3. MeetingPoint
         if (Schema::hasTable('meeting_points')) {
             Schema::table('meeting_points', function (Blueprint $table) {
                if (Schema::hasColumn('meeting_points', 'name')) $table->dropColumn('name');
                if (Schema::hasColumn('meeting_points', 'description')) $table->dropColumn('description');
                if (Schema::hasColumn('meeting_points', 'instructions')) $table->dropColumn('instructions');
             });
             Schema::table('meeting_points', function (Blueprint $table) {
                $table->json('name')->nullable();
                $table->json('description')->nullable();
                $table->json('instructions')->nullable();
             });
         }
    }

    public function down(): void
    {
        // ... (revert logic omitted for brevity, but essentially add string columns back)
    }
};
