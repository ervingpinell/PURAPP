<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Policy;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $policy = Policy::where('slug', 'cancellation-policies')->first();
        if ($policy) {
            $policy->slug = 'cancellations-policies';
            $policy->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $policy = Policy::where('slug', 'cancellations-policies')->first();
        if ($policy) {
            $policy->slug = 'cancellation-policies';
            $policy->save();
        }
    }
};
