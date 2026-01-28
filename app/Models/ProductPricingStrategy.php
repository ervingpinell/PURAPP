<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProductPricingStrategy extends Model
{
    protected $fillable = [
        'product_id',
        'strategy_type',
        'config',
        'is_active',
        'priority',
        'valid_from',
        'valid_until',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function rules()
    {
        return $this->hasMany(ProductPricingRule::class, 'strategy_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValidForDate($query, $date)
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        return $query->where(function ($q) use ($date) {
            $q->whereNull('valid_from')
              ->orWhere('valid_from', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('valid_until')
              ->orWhere('valid_until', '>=', $date);
        });
    }

    // ==========================================
    // PRICING CALCULATIONS
    // ==========================================

    /**
     * Punto de entrada principal para calcular precio
     * 
     * @param int $totalPassengers Total de pasajeros
     * @param array $breakdown Breakdown por categoría (para per_category y tiered_per_category)
     *        Formato: [['category_id' => 1, 'quantity' => 2], ...]
     * @return array
     */
    public function calculatePrice(int $totalPassengers, array $breakdown = []): array
    {
        return match($this->strategy_type) {
            'flat_rate' => $this->calculateFlatRate($totalPassengers),
            'per_person' => $this->calculatePerPerson($totalPassengers),
            'per_category' => $this->calculatePerCategory($breakdown),
            'tiered' => $this->calculateTiered($totalPassengers),
            'tiered_per_category' => $this->calculateTieredPerCategory($totalPassengers, $breakdown),
            default => ['error' => 'Unknown strategy type', 'total' => 0],
        };
    }

    /**
     * CASO 1: Flat Rate
     * Precio fijo por grupo según tamaño
     */
    protected function calculateFlatRate(int $totalPassengers): array
    {
        $rule = $this->rules()
            ->where('min_passengers', '<=', $totalPassengers)
            ->where('max_passengers', '>=', $totalPassengers)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first();

        if (!$rule) {
            return [
                'error' => "No hay pricing configurado para {$totalPassengers} personas",
                'total' => 0,
            ];
        }

        return [
            'total' => (float) $rule->price,
            'strategy' => 'flat_rate',
            'breakdown' => [
                [
                    'label' => $rule->label ?? "Grupo completo ({$totalPassengers} pax)",
                    'price' => (float) $rule->price,
                    'quantity' => 1,
                    'subtotal' => (float) $rule->price,
                    'type' => 'per_group',
                ]
            ],
        ];
    }

    /**
     * CASO 2: Per Person (Simple)
     * Precio único por persona, sin categorías
     */
    protected function calculatePerPerson(int $totalPassengers): array
    {
        $rule = $this->rules()
            ->whereNull('customer_category_id')
            ->where('is_active', true)
            ->first();

        if (!$rule) {
            return ['error' => 'No hay precio configurado', 'total' => 0];
        }

        $total = $rule->price * $totalPassengers;

        return [
            'total' => (float) $total,
            'strategy' => 'per_person',
            'breakdown' => [
                [
                    'label' => 'Por persona',
                    'price' => (float) $rule->price,
                    'quantity' => $totalPassengers,
                    'subtotal' => (float) $total,
                    'type' => 'per_person',
                ]
            ],
        ];
    }

    /**
     * CASO 3: Per Category
     * Precio diferente por categoría de cliente
     */
    protected function calculatePerCategory(array $breakdown): array
    {
        if (empty($breakdown)) {
            return ['error' => 'Breakdown requerido para per_category', 'total' => 0];
        }

        $total = 0;
        $details = [];

        foreach ($breakdown as $item) {
            $categoryId = $item['category_id'];
            $quantity = $item['quantity'];

            $rule = $this->rules()
                ->where('customer_category_id', $categoryId)
                ->where('is_active', true)
                ->first();

            if (!$rule) {
                $details[] = [
                    'error' => "No hay precio para categoría {$categoryId}",
                    'category_id' => $categoryId,
                ];
                continue;
            }

            // Validar cantidad
            if ($quantity < $rule->min_passengers || $quantity > $rule->max_passengers) {
                $details[] = [
                    'error' => "Cantidad {$quantity} fuera de rango ({$rule->min_passengers}-{$rule->max_passengers})",
                    'category_id' => $categoryId,
                ];
                continue;
            }

            $subtotal = $rule->price * $quantity;
            $total += $subtotal;

            $details[] = [
                'category_id' => $categoryId,
                'category_name' => $rule->category->translated ?? 'Unknown',
                'price' => (float) $rule->price,
                'quantity' => $quantity,
                'subtotal' => (float) $subtotal,
                'type' => 'per_person',
            ];
        }

        return [
            'total' => (float) $total,
            'strategy' => 'per_category',
            'breakdown' => $details,
        ];
    }

    /**
     * CASO 4: Tiered (Escalonado)
     * Precio por persona varía según tamaño del grupo
     */
    protected function calculateTiered(int $totalPassengers): array
    {
        $rule = $this->rules()
            ->where('min_passengers', '<=', $totalPassengers)
            ->where('max_passengers', '>=', $totalPassengers)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first();

        if (!$rule) {
            return [
                'error' => "No hay tier configurado para {$totalPassengers} personas",
                'total' => 0
            ];
        }

        $total = $rule->price * $totalPassengers;

        return [
            'total' => (float) $total,
            'strategy' => 'tiered',
            'tier_label' => $rule->label,
            'tier_range' => "{$rule->min_passengers}-{$rule->max_passengers}",
            'breakdown' => [
                [
                    'label' => $rule->label ?? "Tier {$rule->min_passengers}-{$rule->max_passengers}",
                    'price' => (float) $rule->price,
                    'quantity' => $totalPassengers,
                    'subtotal' => (float) $total,
                    'type' => 'per_person',
                ]
            ],
        ];
    }

    /**
     * CASO 5: Tiered Per Category (Híbrido)
     * Precio por categoría + tier según tamaño total
     */
    protected function calculateTieredPerCategory(int $totalPassengers, array $breakdown): array
    {
        if (empty($breakdown)) {
            return ['error' => 'Breakdown requerido', 'total' => 0];
        }

        $total = 0;
        $details = [];

        foreach ($breakdown as $item) {
            $categoryId = $item['category_id'];
            $quantity = $item['quantity'];

            // Buscar regla que aplique al tier total Y categoría
            $rule = $this->rules()
                ->where('customer_category_id', $categoryId)
                ->where('min_passengers', '<=', $totalPassengers)
                ->where('max_passengers', '>=', $totalPassengers)
                ->where('is_active', true)
                ->first();

            if (!$rule) {
                $details[] = [
                    'error' => "No hay precio para categoría {$categoryId} en tier de {$totalPassengers} pax",
                    'category_id' => $categoryId,
                ];
                continue;
            }

            $subtotal = $rule->price * $quantity;
            $total += $subtotal;

            $details[] = [
                'category_id' => $categoryId,
                'category_name' => $rule->category->translated ?? 'Unknown',
                'tier_label' => $rule->label,
                'tier_range' => "{$rule->min_passengers}-{$rule->max_passengers}",
                'price' => (float) $rule->price,
                'quantity' => $quantity,
                'subtotal' => (float) $subtotal,
                'type' => 'per_person',
            ];
        }

        return [
            'total' => (float) $total,
            'strategy' => 'tiered_per_category',
            'total_passengers' => $totalPassengers,
            'breakdown' => $details,
        ];
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Obtiene regla para X pasajeros (flat_rate, tiered)
     */
    public function getRuleForPassengers(int $passengers)
    {
        return $this->rules()
            ->where('min_passengers', '<=', $passengers)
            ->where('max_passengers', '>=', $passengers)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first();
    }

    /**
     * Obtiene regla para tier + categoría (tiered_per_category)
     */
    public function getRuleForTierAndCategory(int $totalPassengers, int $categoryId)
    {
        return $this->rules()
            ->where('customer_category_id', $categoryId)
            ->where('min_passengers', '<=', $totalPassengers)
            ->where('max_passengers', '>=', $totalPassengers)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Validar que la estrategia está completa
     */
    public function validate(): array
    {
        $errors = [];

        switch ($this->strategy_type) {
            case 'flat_rate':
            case 'tiered':
                if ($this->rules()->count() === 0) {
                    $errors[] = 'No hay reglas de pricing configuradas';
                }
                
                // Verificar gaps en rangos
                $rules = $this->rules()->orderBy('min_passengers')->get();
                foreach ($rules as $i => $rule) {
                    if ($i > 0) {
                        $prevRule = $rules[$i - 1];
                        if ($rule->min_passengers > $prevRule->max_passengers + 1) {
                            $errors[] = "Gap entre tier {$prevRule->max_passengers} y {$rule->min_passengers}";
                        }
                    }
                }
                break;

            case 'per_person':
                if ($this->rules()->whereNull('customer_category_id')->count() === 0) {
                    $errors[] = 'Falta regla de precio por persona';
                }
                break;

            case 'per_category':
                $activeCategories = \App\Models\CustomerCategory::active()->count();
                $configuredCategories = $this->rules()
                    ->whereNotNull('customer_category_id')
                    ->distinct('customer_category_id')
                    ->count();
                
                if ($configuredCategories === 0) {
                    $errors[] = 'No hay precios configurados por categoría';
                } elseif ($configuredCategories < $activeCategories) {
                    $errors[] = "Faltan categorías ({$configuredCategories}/{$activeCategories} configuradas)";
                }
                break;

            case 'tiered_per_category':
                // Verificar que cada tier tiene todas las categorías
                $tiers = $this->rules()
                    ->select('min_passengers', 'max_passengers')
                    ->distinct()
                    ->get();

                $activeCategories = \App\Models\CustomerCategory::active()->count();

                foreach ($tiers as $tier) {
                    $categoriesInTier = $this->rules()
                        ->where('min_passengers', $tier->min_passengers)
                        ->where('max_passengers', $tier->max_passengers)
                        ->whereNotNull('customer_category_id')
                        ->distinct('customer_category_id')
                        ->count();

                    if ($categoriesInTier < $activeCategories) {
                        $errors[] = "Tier {$tier->min_passengers}-{$tier->max_passengers}: solo {$categoriesInTier}/{$activeCategories} categorías";
                    }
                }
                break;
        }

        return $errors;
    }

    /**
     * Get strategy display name
     */
    public function getDisplayNameAttribute(): string
    {
        return match($this->strategy_type) {
            'flat_rate' => 'Precio por Grupo',
            'per_person' => 'Precio por Persona',
            'per_category' => 'Precio por Categoría',
            'tiered' => 'Precio Escalonado',
            'tiered_per_category' => 'Precio Escalonado + Categorías',
            default => $this->strategy_type,
        };
    }
}
