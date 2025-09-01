<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $table = 'app_settings';
    protected $fillable = ['key', 'value'];
    public $timestamps = true;

    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("setting:{$key}", function () use ($key, $default) {
            $row = static::query()->where('key', $key)->first();
            return $row ? $row->value : $default;
        });
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        Cache::forget("setting:{$key}");
    }
}
