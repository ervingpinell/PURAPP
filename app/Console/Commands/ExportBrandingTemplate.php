<?php

namespace App\Console\Commands;

use App\Models\BrandingSetting;
use Illuminate\Console\Command;

class ExportBrandingTemplate extends Command
{
    protected $signature = 'branding:export {filename=branding_template.json}';
    protected $description = 'Export current branding settings as a JSON template';

    public function handle()
    {
        $filename = $this->argument('filename');
        
        // Get all branding settings
        $settings = BrandingSetting::all()->mapWithKeys(function ($setting) {
            return [$setting->key => $setting->value];
        })->toArray();

        // Create template structure
        $template = [
            'name' => 'Current Branding Configuration',
            'description' => 'Exported branding template with all current settings',
            'version' => '1.0',
            'exported_at' => now()->toDateTimeString(),
            'settings' => $settings,
        ];

        // Save to file
        $path = storage_path('app/' . $filename);
        file_put_contents($path, json_encode($template, JSON_PRETTY_PRINT));

        $this->info("✅ Branding template exported successfully!");
        $this->info("📁 Location: {$path}");
        $this->info("📊 Total settings: " . count($settings));
        
        return 0;
    }
}
