<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ============================================
        // PARTE 2: RENOMBRAR COLUMNAS Y FOREIGN KEYS
        // ============================================
        
        // 1. product_types
        if (Schema::hasColumn('product_types', 'tour_type_id')) {
            Schema::table('product_types', function (Blueprint $table) {
                $table->renameColumn('tour_type_id', 'product_type_id');
            });
        }
        
        // 2. product2 (tabla principal)
        Schema::table('product2', function (Blueprint $table) {
            // Drop foreign key antes de renombrar
            $this->dropForeignKeyIfExists('product2', 'tours_tour_type_id_foreign');
            $this->dropForeignKeyIfExists('product2', 'tour_type_id');
            
            // Renombrar columnas
            if (Schema::hasColumn('product2', 'tour_id')) {
                $table->renameColumn('tour_id', 'product_id');
            }
            if (Schema::hasColumn('product2', 'tour_type_id')) {
                $table->renameColumn('tour_type_id', 'product_type_id');
            }
        });
        
        // Recrear foreign key con nuevo nombre
        Schema::table('product2', function (Blueprint $table) {
            $table->foreign('product_type_id')
                  ->references('product_type_id')
                  ->on('product_types')
                  ->onDelete('restrict');
        });
        
        // 3. product_availability
        Schema::table('product_availability', function (Blueprint $table) {
            $this->dropForeignKeyIfExists('product_availability', 'tour_availability_tour_id_foreign');
            
            if (Schema::hasColumn('product_availability', 'tour_id')) {
                $table->renameColumn('tour_id', 'product_id');
            }
            
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('product2')
                  ->onDelete('cascade');
        });
        
        // 4. product_excluded_dates
        Schema::table('product_excluded_dates', function (Blueprint $table) {
            $this->dropForeignKeyIfExists('product_excluded_dates', 'tour_excluded_dates_tour_id_foreign');
            
            if (Schema::hasColumn('product_excluded_dates', 'tour_id')) {
                $table->renameColumn('tour_id', 'product_id');
            }
            
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('product2')
                  ->onDelete('cascade');
        });
        
        // 5. product_images
        Schema::table('product_images', function (Blueprint $table) {
            $this->dropForeignKeyIfExists('product_images', 'tour_images_tour_id_foreign');
            
            if (Schema::hasColumn('product_images', 'tour_id')) {
                $table->renameColumn('tour_id', 'product_id');
            }
            
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('product2')
                  ->onDelete('cascade');
        });
        
        // 6. product_prices
        Schema::table('product_prices', function (Blueprint $table) {
            $this->dropForeignKeyIfExists('product_prices', 'tour_prices_tour_id_foreign');
            
            if (Schema::hasColumn('product_prices', 'tour_id')) {
                $table->renameColumn('tour_id', 'product_id');
                $table->renameColumn('tour_price_id', 'product_price_id');
            }
            
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('product2')
                  ->onDelete('cascade');
        });
        
        // 7. product_translations
        if (Schema::hasColumn('product_translations', 'tour_id')) {
            Schema::table('product_translations', function (Blueprint $table) {
                $table->renameColumn('tour_id', 'product_id');
            });
        }
        
        // 8. product_type_translations
        if (Schema::hasColumn('product_type_translations', 'tour_type_id')) {
            Schema::table('product_type_translations', function (Blueprint $table) {
                $table->renameColumn('tour_type_id', 'product_type_id');
            });
        }
        
        // 9. product_audit_log
        if (Schema::hasColumn('product_audit_log', 'tour_id')) {
            Schema::table('product_audit_log', function (Blueprint $table) {
                $table->renameColumn('tour_id', 'product_id');
            });
        }
        
        // 10. bookings (si tiene tour_id)
        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'tour_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $this->dropForeignKeyIfExists('bookings', 'bookings_tour_id_foreign');
                
                $table->renameColumn('tour_id', 'product_id');
                
                $table->foreign('product_id')
                      ->references('product_id')
                      ->on('product2')
                      ->onDelete('set null');
            });
        }
        
        // 11. booking_details
        if (Schema::hasTable('booking_details') && Schema::hasColumn('booking_details', 'tour_id')) {
            Schema::table('booking_details', function (Blueprint $table) {
                $this->dropForeignKeyIfExists('booking_details', 'booking_details_tour_id_foreign');
                
                $table->renameColumn('tour_id', 'product_id');
                
                $table->foreign('product_id')
                      ->references('product_id')
                      ->on('product2')
                      ->onDelete('set null');
            });
        }
        
        // 12. cart_items
        Schema::table('cart_items', function (Blueprint $table) {
            $this->dropForeignKeyIfExists('cart_items', 'cart_items_tour_id_foreign');
            
            if (Schema::hasColumn('cart_items', 'tour_id')) {
                $table->renameColumn('tour_id', 'product_id');
            }
            
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('product2')
                  ->onDelete('cascade');
        });
        
        // 13. reviews (si existe)
        if (Schema::hasTable('reviews') && Schema::hasColumn('reviews', 'tour_id')) {
            Schema::table('reviews', function (Blueprint $table) {
                $this->dropForeignKeyIfExists('reviews', 'reviews_tour_id_foreign');
                
                $table->renameColumn('tour_id', 'product_id');
                
                $table->foreign('product_id')
                      ->references('product_id')
                      ->on('product2')
                      ->onDelete('cascade');
            });
        }
        
        // 14. Tablas pivot
        if (Schema::hasColumn('amenity_product', 'tour_id')) {
            Schema::table('amenity_product', function (Blueprint $table) {
                $table->renameColumn('tour_id', 'product_id');
            });
        }
        
        if (Schema::hasColumn('product_language_product', 'tour_id')) {
            Schema::table('product_language_product', function (Blueprint $table) {
                $table->renameColumn('tour_id', 'product_id');
            });
        }
        
        if (Schema::hasColumn('schedule_product', 'tour_id')) {
            Schema::table('schedule_product', function (Blueprint $table) {
                $table->renameColumn('tour_id', 'product_id');
            });
        }
        
        if (Schema::hasTable('excluded_product_amenities') && Schema::hasColumn('excluded_product_amenities', 'tour_id')) {
            Schema::table('excluded_product_amenities', function (Blueprint $table) {
                $table->renameColumn('tour_id', 'product_id');
            });
        }
        
        if (Schema::hasTable('product_type_product_order')) {
            if (Schema::hasColumn('product_type_product_order', 'tour_id')) {
                Schema::table('product_type_product_order', function (Blueprint $table) {
                    $table->renameColumn('tour_id', 'product_id');
                });
            }
            if (Schema::hasColumn('product_type_product_order', 'tour_type_id')) {
                Schema::table('product_type_product_order', function (Blueprint $table) {
                    $table->renameColumn('tour_type_id', 'product_type_id');
                });
            }
        }
    }

    public function down(): void
    {
        // Implementar rollback si es absolutamente necesario
        // NO RECOMENDADO - mejor usar backup
    }
    
    /**
     * Helper para drop foreign key si existe
     */
    protected function dropForeignKeyIfExists(string $table, string $foreignKey): void
    {
        // PostgreSQL approach
        DB::statement("ALTER TABLE \"{$table}\" DROP CONSTRAINT IF EXISTS \"{$foreignKey}\"");
    }
};
