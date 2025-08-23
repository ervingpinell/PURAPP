<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class Policy extends Model
{
    use HasFactory;

    protected $table = 'policies';
    protected $primaryKey = 'policy_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'is_default',
        'is_active',
        'effective_from',
        'effective_to',
    ];

    protected $casts = [
        'is_default'     => 'bool',
        'is_active'      => 'bool',
        'effective_from' => 'date',
        'effective_to'   => 'date',
    ];

    public function getRouteKeyName(): string
    {
        return 'policy_id';
    }


    public function translations()
    {
        return $this->hasMany(PolicyTranslation::class, 'policy_id', 'policy_id');
    }

    public function sections()
    {
        return $this->hasMany(PolicySection::class, 'policy_id', 'policy_id')
            ->orderBy('sort_order')
            ->orderBy('section_id');
    }

    public function activeSections()
    {
        return $this->sections()->where('is_active', true);
    }


    public static function canonicalLocale(string $loc): string
    {
        $loc   = str_replace('-', '_', trim($loc));
        $short = strtolower(substr($loc, 0, 2));

        return match ($short) {
            'es' => 'es',
            'en' => 'en',
            'fr' => 'fr',
            'de' => 'de',
            'pt' => 'pt_BR',
            default => $loc,
        };
    }

    public function translation(?string $locale = null)
    {
        $requested = self::canonicalLocale($locale ?: app()->getLocale());
        $fallback  = self::canonicalLocale((string) config('app.fallback_locale', 'es'));

        $bag = $this->relationLoaded('translations')
            ? $this->getRelation('translations')
            : $this->translations()->get();

        $candidates = array_values(array_unique([
            $requested,
            strtolower($requested),
            strtoupper($requested),
            str_replace('_', '-', $requested),
            str_replace('-', '_', $requested),
            substr($requested, 0, 2),
        ]));

        $found = $bag->first(function ($t) use ($candidates) {
            $v = (string) ($t->locale ?? '');
            $norms = [
                $v,
                strtolower($v),
                strtoupper($v),
                str_replace('-', '_', $v),
                str_replace('_', '-', $v),
                substr($v, 0, 2),
            ];
            return count(array_intersect($candidates, $norms)) > 0;
        });

        if ($found) return $found;

        return $bag->firstWhere('locale', $fallback)
            ?: $bag->firstWhere('locale', substr($fallback, 0, 2))
            ?: $bag->first();
    }

    public function translate(?string $locale = null)
    {
        return $this->translation($locale);
    }

    public function getTitleTranslatedAttribute(): ?string
    {
        return optional($this->translation())?->title;
    }

    public function getContentTranslatedAttribute(): ?string
    {
        return optional($this->translation())?->content;
    }


    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeEffectiveOn($q, ?Carbon $date = null)
    {
        $d = ($date ?: now())->toDateString();

        return $q->where(function ($qq) use ($d) {
                $qq->whereNull('effective_from')->orWhereDate('effective_from', '<=', $d);
            })->where(function ($qq) use ($d) {
                $qq->whereNull('effective_to')->orWhereDate('effective_to', '>=', $d);
            });
    }

    public function scopeType($q, string $type)
    {
        if (Schema::hasColumn($this->getTable(), 'type')) {
            return $q->where('type', $type);
        }
        return $q;
    }


    public static function byType(string $type): ?self
    {
        $q = static::query()->active();

        if (Schema::hasColumn((new static)->getTable(), 'type')) {
            return $q->where('type', $type)
                ->orderByDesc('is_default')
                ->orderByDesc('effective_from')
                ->first();
        }

        $map = [
            'terminos'    => 'Términos y Condiciones',
            'cancelacion' => 'Política de Cancelación',
            'reembolso'   => 'Política de Reembolsos',
            'privacidad'  => 'Política de Privacidad',
        ];

        if (isset($map[$type])) {
            $base = $map[$type];

            $q->where(function ($qq) use ($base) {
                $qq->where('name', $base)
                   ->orWhere('name', 'like', $base.'%');
            });

            return $q->orderByDesc('effective_from')->first();
        }

        return $q->whereHas('translations', function ($qq) use ($type) {
            $qq->where('title', 'like', '%'.$type.'%');
        })->orderByDesc('effective_from')->first();
    }
}
