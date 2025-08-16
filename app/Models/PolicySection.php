<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PolicySection extends Model
{
    use HasFactory;

    protected $table = 'policy_sections';
    protected $primaryKey = 'section_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = ['policy_id','key','sort_order','is_active'];
    protected $casts = ['is_active' => 'bool'];

    public function policy()
    {
        return $this->belongsTo(Policy::class, 'policy_id', 'policy_id');
    }

    public function translations()
    {
        return $this->hasMany(PolicySectionTranslation::class, 'section_id', 'section_id');
    }

    public static function canonicalLocale(string $loc): string
    {
        $loc   = str_replace('-', '_', trim($loc));
        $short = strtolower(substr($loc, 0, 2));
        return match ($short) {
            'es' => 'es', 'en' => 'en', 'fr' => 'fr', 'de' => 'de', 'pt' => 'pt_BR', default => $loc,
        };
    }

    public function translation(?string $locale = null)
    {
        $requested = self::canonicalLocale($locale ?: app()->getLocale());
        $fallback  = self::canonicalLocale((string) config('app.fallback_locale', 'es'));

        $bag = $this->relationLoaded('translations')
            ? $this->getRelation('translations')
            : $this->translations()->get();

        $cands = array_values(array_unique([
            $requested, strtolower($requested), strtoupper($requested),
            str_replace('_','-',$requested), str_replace('-','_',$requested),
            substr($requested,0,2),
        ]));

        $found = $bag->first(function($t) use($cands){
            $v = (string) ($t->locale ?? '');
            $norms = [$v, strtolower($v), strtoupper($v), str_replace('-','_',$v), str_replace('_','-',$v), substr($v,0,2)];
            return count(array_intersect($cands, $norms)) > 0;
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
}
