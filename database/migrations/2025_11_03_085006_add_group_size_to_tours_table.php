<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->integer('group_size')->nullable()->after('max_capacity')
                ->comment('Tamaño máximo del grupo (mostrado en frontend)');
        });
    }

    public function down()
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn('group_size');
        });
    }
};
