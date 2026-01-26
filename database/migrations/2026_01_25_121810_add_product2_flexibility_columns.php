<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product2', function (Blueprint $table) {
            // Categoría de producto
            if (!Schema::hasColumn('product2', 'product_category')) {
                $table->enum('product_category', [
                    'guided_tour',
                    'private_transfer',
                    'shuttle_service',
                    'adventure_activity',
                    'equipment_rental',
                    'combo_package',
                    'attraction_pass'
                ])->default('guided_tour')->after('product_type_id');
            }
            
            // Configuración de flexibilidad
            if (!Schema::hasColumn('product2', 'allow_custom_time')) {
                $table->boolean('allow_custom_time')->default(false)
                      ->comment('Cliente puede elegir hora personalizada');
            }
            
            if (!Schema::hasColumn('product2', 'allow_custom_pickup')) {
                $table->boolean('allow_custom_pickup')->default(false)
                      ->comment('Cliente puede elegir punto de recogida personalizado');
            }
            
            if (!Schema::hasColumn('product2', 'requires_vehicle_assignment')) {
                $table->boolean('requires_vehicle_assignment')->default(false)
                      ->comment('Requiere asignación de vehículo (para transfers)');
            }
            
            if (!Schema::hasColumn('product2', 'custom_fields_config')) {
                $table->json('custom_fields_config')->nullable()
                      ->comment('Configuración de campos personalizados JSON');
            }
        });
        
        // Tabla para zonas de pickup personalizadas
        if (!Schema::hasTable('product_pickup_zones')) {
            Schema::create('product_pickup_zones', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->string('zone_name');
                $table->decimal('price_modifier', 10, 2)->default(0);
                $table->text('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                
                $table->foreign('product_id')
                      ->references('product_id')
                      ->on('product2')
                      ->onDelete('cascade');
                      
                $table->index('product_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_pickup_zones');
        
        Schema::table('product2', function (Blueprint $table) {
            $columns = [
                'product_category',
                'allow_custom_time',
                'allow_custom_pickup',
                'requires_vehicle_assignment',
                'custom_fields_config'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('product2', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
