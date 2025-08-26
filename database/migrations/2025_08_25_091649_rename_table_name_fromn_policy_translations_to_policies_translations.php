<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::rename('policy_translations', 'policies_translations');
    }

    public function down(): void
    {
        Schema::rename('policies_translations', 'policy_translations');
    }
};
