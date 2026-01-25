<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\BrandingSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        BrandingSetting::firstOrCreate(
            ['key' => 'background_opacity'],
            [
                'value' => '0.95',
                'type' => 'number',
                'category' => 'effects',
                'description' => 'Background image opacity (0.0 to 1.0)',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        BrandingSetting::where('key', 'background_opacity')->delete();
    }
};
