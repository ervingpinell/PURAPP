<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandingTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Create a template from current branding settings
     */
    public static function createFromCurrent(string $name, ?string $description = null): self
    {
        $settings = BrandingSetting::all()->mapWithKeys(function ($setting) {
            return [$setting->key => $setting->value];
        })->toArray();

        return self::create([
            'name' => $name,
            'description' => $description,
            'settings' => $settings,
            'is_active' => false,
        ]);
    }

    /**
     * Apply this template to current branding
     */
    public function apply(): void
    {
        foreach ($this->settings as $key => $value) {
            BrandingSetting::set($key, $value);
        }

        BrandingSetting::clearCache();
    }

    /**
     * Export template as JSON
     */
    public function export(): string
    {
        return json_encode([
            'name' => $this->name,
            'description' => $this->description,
            'version' => '1.0',
            'created_at' => $this->created_at->toDateTimeString(),
            'settings' => $this->settings,
        ], JSON_PRETTY_PRINT);
    }

    /**
     * Import template from JSON
     */
    public static function import(string $json): self
    {
        $data = json_decode($json, true);

        return self::create([
            'name' => $data['name'] ?? 'Imported Template',
            'description' => $data['description'] ?? null,
            'settings' => $data['settings'] ?? [],
            'is_active' => false,
        ]);
    }
}
