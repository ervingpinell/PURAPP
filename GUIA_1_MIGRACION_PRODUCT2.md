# 📘 GUÍA 1: MIGRACIÓN TOURS → PRODUCT2

**Proyecto:** PURAPP  
**Objetivo:** Migrar sistema de tours a product2 para soportar múltiples verticales  
**Tiempo Estimado:** 6-8 horas  
**Prioridad:** ALTA - Base para todo lo demás

---

## 📋 TABLA DE CONTENIDOS

1. [Pre-requisitos](#pre-requisitos)
2. [Backup y Seguridad](#backup-y-seguridad)
3. [Fase 1: Migraciones de Base de Datos](#fase-1-migraciones-de-base-de-datos)
4. [Fase 2: Actualización de Modelos](#fase-2-actualización-de-modelos)
5. [Fase 3: Actualización de Controladores](#fase-3-actualización-de-controladores)
6. [Fase 4: Actualización de Vistas](#fase-4-actualización-de-vistas)
7. [Fase 5: Rutas y Middlewares](#fase-5-rutas-y-middlewares)
8. [Fase 6: Testing y Verificación](#fase-6-testing-y-verificación)
9. [Checklist Final](#checklist-final)

---

## PRE-REQUISITOS

### ✅ Antes de Empezar

```bash
# 1. Verificar rama actual
git status
git checkout develop  # o main

# 2. Crear rama para la migración
git checkout -b feature/migrate-to-product2

# 3. Verificar migraciones actuales
php artisan migrate:status

# 4. Instalar dependencias necesarias
composer require doctrine/dbal
```

### 📦 Dependencias

```json
{
  "doctrine/dbal": "^3.0"  // Para renombrar columnas
}
```

---

## BACKUP Y SEGURIDAD

### 🔒 CRÍTICO: Hacer Backup

```bash
# 1. Backup de base de datos
# Opción A: mysqldump
mysqldump -u root -p purapp_db > backup_pre_product2_$(date +%Y%m%d_%H%M%S).sql

# Opción B: Laravel backup (si está configurado)
php artisan backup:run

# 2. Commit del código actual
git add .
git commit -m "checkpoint: antes de migración product2"

# 3. Tag del estado actual (por si acaso)
git tag -a v1.0-pre-product2 -m "Before product2 migration"
```

---

## FASE 1: MIGRACIONES DE BASE DE DATOS

### Paso 1.1: Crear Migraciones

```bash
# Crear las 4 migraciones necesarias
php artisan make:migration rename_tours_to_product2_part1_tables
php artisan make:migration rename_tours_to_product2_part2_columns
php artisan make:migration add_product2_flexibility_columns
php artisan make:migration update_views_for_product2
```

---

### Migración 1: Renombrar Tablas

**Archivo:** `database/migrations/2026_01_26_000001_rename_tours_to_product2_part1_tables.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ============================================
        // PARTE 1: RENOMBRAR TABLAS PRINCIPALES
        // ============================================
        
        // 1. Tipos
        if (Schema::hasTable('tour_types')) {
            Schema::rename('tour_types', 'product_types');
        }
        
        // 2. Tabla principal
        if (Schema::hasTable('tours')) {
            Schema::rename('tours', 'product2');
        }
        
        // 3. Tablas relacionadas
        $renames = [
            'tour_availability' => 'product_availability',
            'tour_excluded_dates' => 'product_excluded_dates',
            'tour_images' => 'product_images',
            'tour_prices' => 'product_prices',
            'tour_translations' => 'product_translations',
            'tour_type_translations' => 'product_type_translations',
            'tour_audit_log' => 'product_audit_log',
        ];
        
        foreach ($renames as $old => $new) {
            if (Schema::hasTable($old) && !Schema::hasTable($new)) {
                Schema::rename($old, $new);
            }
        }
        
        // 4. Tablas pivot
        $pivotRenames = [
            'amenity_tour' => 'amenity_product',
            'tour_language_tour' => 'product_language_product',
            'schedule_tour' => 'schedule_product',
            'excluded_tour_amenities' => 'excluded_product_amenities',
            'tour_type_tour_order' => 'product_type_product_order',
        ];
        
        foreach ($pivotRenames as $old => $new) {
            if (Schema::hasTable($old) && !Schema::hasTable($new)) {
                Schema::rename($old, $new);
            }
        }
    }

    public function down(): void
    {
        // Revertir en orden inverso
        $pivotRenames = [
            'product_type_product_order' => 'tour_type_tour_order',
            'excluded_product_amenities' => 'excluded_tour_amenities',
            'schedule_product' => 'schedule_tour',
            'product_language_product' => 'tour_language_tour',
            'amenity_product' => 'amenity_tour',
        ];
        
        foreach ($pivotRenames as $old => $new) {
            if (Schema::hasTable($old)) {
                Schema::rename($old, $new);
            }
        }
        
        $renames = [
            'product_audit_log' => 'tour_audit_log',
            'product_type_translations' => 'tour_type_translations',
            'product_translations' => 'tour_translations',
            'product_prices' => 'tour_prices',
            'product_images' => 'tour_images',
            'product_excluded_dates' => 'tour_excluded_dates',
            'product_availability' => 'tour_availability',
        ];
        
        foreach ($renames as $old => $new) {
            if (Schema::hasTable($old)) {
                Schema::rename($old, $new);
            }
        }
        
        if (Schema::hasTable('product2')) {
            Schema::rename('product2', 'tours');
        }
        
        if (Schema::hasTable('product_types')) {
            Schema::rename('product_types', 'tour_types');
        }
    }
};
```

---

### Migración 2: Renombrar Columnas y Foreign Keys

**Archivo:** `database/migrations/2026_01_26_000002_rename_tours_to_product2_part2_columns.php`

```php
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
        $connection = Schema::getConnection();
        $doctrineTable = $connection->getDoctrineSchemaManager()
            ->listTableDetails($table);
        
        foreach ($doctrineTable->getForeignKeys() as $fk) {
            if ($fk->getName() === $foreignKey || 
                str_contains($fk->getName(), $foreignKey)) {
                Schema::table($table, function (Blueprint $table) use ($fk) {
                    $table->dropForeign($fk->getName());
                });
                break;
            }
        }
    }
};
```

---

### Migración 3: Agregar Columnas de Flexibilidad

**Archivo:** `database/migrations/2026_01_26_000003_add_product2_flexibility_columns.php`

```php
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
```

---

### Migración 4: Actualizar Vistas de Base de Datos

**Archivo:** `database/migrations/2026_01_26_000004_update_views_for_product2.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop vistas existentes
        DB::statement('DROP VIEW IF EXISTS v_booking_facts');
        DB::statement('DROP VIEW IF EXISTS v_booking_category_facts');
        
        // Recrear v_booking_facts con nuevos nombres
        DB::statement("
            CREATE VIEW v_booking_facts AS
            SELECT 
                b.booking_id,
                b.product_id,
                b.booking_date,
                b.status,
                b.total_amount,
                b.created_at,
                b.user_id,
                p.name as product_name,
                p.product_category,
                pt.name as product_type_name,
                u.first_name as customer_first_name,
                u.last_name as customer_last_name,
                u.email as customer_email
            FROM bookings b
            LEFT JOIN product2 p ON b.product_id = p.product_id
            LEFT JOIN product_types pt ON p.product_type_id = pt.product_type_id
            LEFT JOIN users u ON b.user_id = u.id
        ");
        
        // Recrear v_booking_category_facts
        DB::statement("
            CREATE VIEW v_booking_category_facts AS
            SELECT 
                bd.id as booking_detail_id,
                bd.booking_id,
                bd.product_id,
                bd.customer_category_id,
                bd.quantity,
                bd.unit_price,
                bd.subtotal,
                cc.slug as category_slug,
                p.name as product_name,
                p.product_category
            FROM booking_details bd
            LEFT JOIN customer_categories cc ON bd.customer_category_id = cc.category_id
            LEFT JOIN product2 p ON bd.product_id = p.product_id
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_booking_facts');
        DB::statement('DROP VIEW IF EXISTS v_booking_category_facts');
        
        // Recrear vistas originales si es necesario
        // (copiar de migraciones originales)
    }
};
```

---

### Ejecutar Migraciones

```bash
# Ver qué migraciones se van a ejecutar
php artisan migrate:status

# Ejecutar las 4 migraciones
php artisan migrate

# Verificar que todo salió bien
php artisan migrate:status

# Si algo falla, rollback solo estas 4
php artisan migrate:rollback --step=4
```

---

## FASE 2: ACTUALIZACIÓN DE MODELOS

### Paso 2.1: Renombrar Archivos de Modelos

```bash
# Desde la raíz del proyecto

# 1. Modelo principal
mv app/Models/Tour.php app/Models/Product.php

# 2. Tipo de producto
mv app/Models/TourType.php app/Models/ProductType.php

# 3. Modelos relacionados
mv app/Models/TourAvailability.php app/Models/ProductAvailability.php
mv app/Models/TourExcludedDate.php app/Models/ProductExcludedDate.php
mv app/Models/TourImage.php app/Models/ProductImage.php
mv app/Models/TourPrice.php app/Models/ProductPrice.php
mv app/Models/TourTranslation.php app/Models/ProductTranslation.php
mv app/Models/TourTypeTranslation.php app/Models/ProductTypeTranslation.php
mv app/Models/TourAuditLog.php app/Models/ProductAuditLog.php
```

---

### Paso 2.2: Actualizar Product.php

**Archivo:** `app/Models/Product.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $table = 'product2';
    protected $primaryKey = 'product_id';
    
    public $translatable = ['name', 'description', 'overview', 'recommendations'];

    protected $fillable = [
        'product_type_id',
        'product_category',
        'name',
        'description',
        'overview',
        'recommendations',
        'length',
        'max_capacity',
        'is_active',
        'color',
        'slug',
        'itinerary_id',
        'cutoff_lead',
        'allow_custom_time',
        'allow_custom_pickup',
        'requires_vehicle_assignment',
        'custom_fields_config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allow_custom_time' => 'boolean',
        'allow_custom_pickup' => 'boolean',
        'requires_vehicle_assignment' => 'boolean',
        'custom_fields_config' => 'array',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['cover_image', 'display_category'];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function productType()
    {
        return $this->belongsTo(ProductType::class, 'product_type_id', 'product_type_id');
    }
    
    public function type()
    {
        return $this->productType();
    }

    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class, 'itinerary_id', 'itinerary_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id')
                    ->orderBy('sort_order');
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class, 'product_id', 'product_id');
    }

    public function availability()
    {
        return $this->hasMany(ProductAvailability::class, 'product_id', 'product_id');
    }

    public function excludedDates()
    {
        return $this->hasMany(ProductExcludedDate::class, 'product_id', 'product_id');
    }

    public function schedules()
    {
        return $this->belongsToMany(
            Schedule::class,
            'schedule_product',
            'product_id',
            'schedule_id'
        )->withPivot(['is_active', 'cutoff_lead'])
          ->withTimestamps();
    }

    public function languages()
    {
        return $this->belongsToMany(
            TourLanguage::class,
            'product_language_product',
            'product_id',
            'tour_language_id'
        )->withTimestamps();
    }

    public function amenities()
    {
        return $this->belongsToMany(
            Amenity::class,
            'amenity_product',
            'product_id',
            'amenity_id'
        )->withTimestamps();
    }

    public function excludedAmenities()
    {
        return $this->belongsToMany(
            Amenity::class,
            'excluded_product_amenities',
            'product_id',
            'amenity_id'
        )->withTimestamps();
    }

    public function pickupZones()
    {
        return $this->hasMany(ProductPickupZone::class, 'product_id', 'product_id')
                    ->orderBy('sort_order');
    }

    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class, 'product_id', 'product_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_id', 'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'product_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(ProductAuditLog::class, 'product_id', 'product_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('product_category', $category);
    }

    public function scopeTours($query)
    {
        return $query->where('product_category', 'guided_tour');
    }

    public function scopeTransfers($query)
    {
        return $query->whereIn('product_category', ['private_transfer', 'shuttle_service']);
    }

    public function scopeActivities($query)
    {
        return $query->where('product_category', 'adventure_activity');
    }

    // ==========================================
    // ACCESSORS & MUTATORS
    // ==========================================

    public function getCoverImageAttribute()
    {
        return $this->images()->first()?->path ?? '/images/placeholder-tour.jpg';
    }

    public function getDisplayCategoryAttribute()
    {
        return match($this->product_category) {
            'guided_tour' => __('Tour Guiado'),
            'private_transfer' => __('Transfer Privado'),
            'shuttle_service' => __('Servicio de Shuttle'),
            'adventure_activity' => __('Actividad de Aventura'),
            'equipment_rental' => __('Alquiler de Equipo'),
            'combo_package' => __('Paquete Combo'),
            'attraction_pass' => __('Pase de Atracción'),
            default => $this->product_category,
        };
    }

    public function getIsTransferAttribute()
    {
        return in_array($this->product_category, ['private_transfer', 'shuttle_service']);
    }

    public function getIsTourAttribute()
    {
        return $this->product_category === 'guided_tour';
    }

    // ==========================================
    // AUTO SLUG
    // ==========================================
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // ==========================================
    // BACKWARD COMPATIBILITY (TEMPORAL)
    // ==========================================

    public function tourType()
    {
        return $this->productType();
    }
    
    public function getTourIdAttribute()
    {
        return $this->product_id;
    }
}
```

---

### Paso 2.3: Crear ProductPickupZone.php

**Archivo:** `app/Models/ProductPickupZone.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPickupZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'zone_name',
        'price_modifier',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'price_modifier' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
```

---

### Paso 2.4: Actualizar ProductType.php

**Archivo:** `app/Models/ProductType.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ProductType extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $table = 'product_types';
    protected $primaryKey = 'product_type_id';
    
    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'duration',
        'cover_path',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'product_type_id', 'product_type_id');
    }

    // Backward compatibility
    public function tours()
    {
        return $this->products();
    }
}
```

---

### Paso 2.5: Actualizar Modelos Relacionados

**Ejemplos de cambios necesarios:**

#### BookingDetail.php
```php
// Cambiar fillable
protected $fillable = [
    'booking_id',
    'product_id',  // antes tour_id
    // ...
];

// Actualizar relación
public function product()
{
    return $this->belongsTo(Product::class, 'product_id', 'product_id');
}

// Backward compatibility (temporal)
public function tour()
{
    return $this->product();
}
```

#### CartItem.php
```php
protected $fillable = [
    'cart_id',
    'product_id',  // antes tour_id
    // ...
];

public function product()
{
    return $this->belongsTo(Product::class, 'product_id', 'product_id');
}
```

#### Booking.php
```php
// Si tiene relación con tour
public function product()
{
    return $this->belongsTo(Product::class, 'product_id', 'product_id');
}
```

---

## FASE 3: ACTUALIZACIÓN DE CONTROLADORES

### Paso 3.1: Renombrar Controladores

```bash
# Admin Controllers
mv app/Http/Controllers/Admin/TourController.php app/Http/Controllers/Admin/ProductController.php

# Opcional: mantener TourController en public para URLs
# Decidir caso por caso
```

---

### Paso 3.2: Actualizar ProductController.php

**Archivo:** `app/Http/Controllers/Admin/ProductController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['productType', 'images']);

        // Filtros
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        if ($request->has('type_id')) {
            $query->where('product_type_id', $request->type_id);
        }

        if (!$request->has('show_inactive')) {
            $query->active();
        }

        $products = $query->latest()->paginate(20);
        $productTypes = ProductType::all();

        return view('admin.products.index', compact('products', 'productTypes'));
    }

    public function create()
    {
        $productTypes = ProductType::all();
        return view('admin.products.create', compact('productTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_type_id' => 'required|exists:product_types,product_type_id',
            'product_category' => 'required|in:guided_tour,private_transfer,shuttle_service,adventure_activity,equipment_rental,combo_package,attraction_pass',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_capacity' => 'required|integer|min:1',
            'allow_custom_time' => 'boolean',
            'allow_custom_pickup' => 'boolean',
            'requires_vehicle_assignment' => 'boolean',
        ]);

        $product = Product::create($validated);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Producto creado exitosamente');
    }

    public function edit(Product $product)
    {
        $product->load(['productType', 'images', 'prices', 'schedules', 'amenities']);
        $productTypes = ProductType::all();

        return view('admin.products.edit', compact('product', 'productTypes'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_type_id' => 'required|exists:product_types,product_type_id',
            'product_category' => 'required',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_capacity' => 'required|integer|min:1',
            'allow_custom_time' => 'boolean',
            'allow_custom_pickup' => 'boolean',
            'requires_vehicle_assignment' => 'boolean',
        ]);

        $product->update($validated);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Producto eliminado exitosamente');
    }
}
```

---

## FASE 4: ACTUALIZACIÓN DE VISTAS

### Paso 4.1: Find & Replace Global en Vistas

**En tu IDE (VSCode, PHPStorm):**

```
# Buscar y reemplazar en todos los .blade.php

Find: \$tour->
Replace: $product->

Find: \$tours
Replace: $products

Find: @foreach\(\$tours as \$tour\)
Replace: @foreach($products as $product)

Find: tour_id
Replace: product_id

Find: tourType
Replace: productType

Find: route\('([^']+)\.tours\.
Replace: route('$1.products.
```

---

### Paso 4.2: Renombrar Directorios de Vistas

```bash
# Admin views
mv resources/views/admin/tours resources/views/admin/products

# Public views - MANTENER /tours para SEO
# Solo actualizar contenido interno
```

---

### Paso 4.3: Ejemplo Vista Admin Index

**Archivo:** `resources/views/admin/products/index.blade.php`

```blade
@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Productos</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Producto
        </a>
    </div>
@stop

@section('content')
    {{-- Filtros --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" class="form-inline">
                <select name="category" class="form-control mr-2">
                    <option value="">Todas las Categorías</option>
                    <option value="guided_tour">Tours Guiados</option>
                    <option value="private_transfer">Transfers Privados</option>
                    <option value="shuttle_service">Shuttle</option>
                    <option value="adventure_activity">Actividades</option>
                </select>
                
                <select name="type_id" class="form-control mr-2">
                    <option value="">Todos los Tipos</option>
                    @foreach($productTypes as $type)
                        <option value="{{ $type->product_type_id }}">
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                
                <button type="submit" class="btn btn-secondary">Filtrar</button>
            </form>
        </div>
    </div>

    {{-- Lista --}}
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Tipo</th>
                        <th>Capacidad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $product->product_id }}</td>
                            <td>
                                <img src="{{ $product->cover_image }}" 
                                     width="50" height="50" 
                                     class="img-thumbnail">
                            </td>
                            <td>{{ $product->name }}</td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $product->display_category }}
                                </span>
                            </td>
                            <td>{{ $product->productType->name ?? 'N/A' }}</td>
                            <td>{{ $product->max_capacity }}</td>
                            <td>
                                <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.products.edit', $product) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                No hay productos registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer">
            {{ $products->links() }}
        </div>
    </div>
@stop
```

---

## FASE 5: RUTAS Y MIDDLEWARES

### Actualizar routes/web.php

```php
<?php

use App\Http\Controllers\Admin\ProductController;

// Admin routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    
    // Products CRUD
    Route::resource('products', ProductController::class);
    
    // Otros recursos relacionados
    Route::resource('product-types', ProductTypeController::class);
});

// Public routes - MANTENER /tours para SEO
Route::get('/tours', [PublicProductController::class, 'index'])->name('tours.index');
Route::get('/tours/{product:slug}', [PublicProductController::class, 'show'])->name('tours.show');
```

---

## FASE 6: TESTING Y VERIFICACIÓN

### Paso 6.1: Limpiar Caché

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

---

### Paso 6.2: Verificar Base de Datos

```sql
-- Verificar que las tablas existen
SHOW TABLES LIKE 'product%';

-- Contar registros migrados
SELECT COUNT(*) FROM product2;
SELECT COUNT(*) FROM product_types;
SELECT COUNT(*) FROM product_prices;

-- Verificar foreign keys
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'purapp_db'
AND TABLE_NAME LIKE 'product%'
AND REFERENCED_TABLE_NAME IS NOT NULL;
```

---

### Paso 6.3: Testing Manual

```bash
✅ Crear producto nuevo
   → Admin > Productos > Nuevo
   → Llenar formulario
   → Guardar

✅ Editar producto existente
   → Admin > Productos > Lista
   → Click Editar
   → Modificar campos
   → Guardar

✅ Ver lista de productos
   → Admin > Productos
   → Verificar filtros
   → Probar paginación

✅ Eliminar producto
   → Admin > Productos > Eliminar
   → Verificar soft delete

✅ Vista pública
   → /tours
   → /tours/{slug}
   → Verificar imágenes

✅ Crear booking
   → Agregar al carrito
   → Checkout
   → Verificar en DB

✅ Reportes
   → Admin > Reportes
   → Verificar datos correctos
```

---

## CHECKLIST FINAL

### ✅ Base de Datos
- [ ] Tablas renombradas
- [ ] Columnas actualizadas
- [ ] Foreign keys funcionando
- [ ] Vistas actualizadas
- [ ] Indexes intactos

### ✅ Modelos
- [ ] Product.php creado
- [ ] ProductType.php actualizado
- [ ] Relaciones funcionando
- [ ] Scopes correctos
- [ ] Accessors/Mutators OK

### ✅ Controladores
- [ ] ProductController funcional
- [ ] Validaciones correctas
- [ ] Responses apropiadas

### ✅ Vistas
- [ ] Variables renombradas
- [ ] Rutas funcionando
- [ ] Assets cargando

### ✅ Rutas
- [ ] Admin routes OK
- [ ] Public routes OK
- [ ] Named routes correctas

### ✅ Funcionalidad
- [ ] Crear producto ✓
- [ ] Editar producto ✓
- [ ] Eliminar producto ✓
- [ ] Ver productos ✓
- [ ] Crear booking ✓
- [ ] Carrito ✓
- [ ] Checkout ✓

---

## 🚨 TROUBLESHOOTING

### Error: "Column not found: tour_id"

```php
// Verificar que actualizaste TODOS los modelos relacionados
// Buscar en todo el proyecto:
grep -r "tour_id" app/Models/
```

### Error: "Base table or view not found: tours"

```php
// Verificar que la migración se ejecutó
php artisan migrate:status

// Si no, ejecutar:
php artisan migrate
```

### Error: Foreign key constraint fails

```php
// Probablemente olvidaste actualizar una tabla relacionada
// Revisar la migración parte 2
```

---

## 📝 COMANDOS DE EMERGENCIA

```bash
# Rollback completo (4 migraciones)
php artisan migrate:rollback --step=4

# Restaurar desde backup
mysql -u root -p purapp_db < backup_pre_product2_TIMESTAMP.sql

# Volver código anterior
git reset --hard v1.0-pre-product2

# Limpiar todo
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

---

## ✅ SIGUIENTE PASO

Una vez completada esta guía, proceder con:

**→ GUÍA 2: Landing Dinámico + Business Type + Branding**

---

**¡Éxito con la migración, mae!** 🚀
