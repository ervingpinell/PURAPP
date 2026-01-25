<?php

namespace App\Http\Controllers;

use App\Models\BrandingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BrandingController extends Controller
{
    public function __construct()
    {
        // Protect with Spatie permission
        $this->middleware('permission:manage branding');
    }

    /**
     * Display the branding management interface
     */
    public function index()
    {
        // Get all settings, ordered by key for consistent display
        $allSettings = BrandingSetting::orderBy('key', 'asc')->get();
        
        // Group by category while maintaining the sort order
        $settings = $allSettings->groupBy('category');
        
        return view('admin.branding.index', compact('settings'));
    }

    /**
     * Update branding settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Log text_shadow values received
        $textShadowSettings = array_filter($request->input('settings', []), function($key) {
            return str_starts_with($key, 'text_shadow_');
        }, ARRAY_FILTER_USE_KEY);
        
        \Log::info('🟢 Text Shadow settings received in request:', $textShadowSettings);

        // Get all settings to properly handle booleans
        $allSettings = BrandingSetting::all()->keyBy('key');

        // First, handle file uploads separately
        if ($request->hasFile('settings')) {
            foreach ($request->file('settings') as $key => $file) {
                $setting = $allSettings->get($key);
                
                if (!$setting || $setting->type !== 'file') {
                    continue;
                }
                
                \Log::info("File upload detected for {$key}", [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'old_value' => $setting->value
                ]);
                
                // Delete old file if exists
                if ($setting->value) {
                    // Remove /storage/ prefix if present
                    $oldPath = str_replace('/storage/', '', $setting->value);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                        \Log::info("Deleted old file: {$oldPath}");
                    }
                }
                
                // Store new file
                $path = $file->store('branding', 'public');
                $value = '/storage/' . $path;
                
                \Log::info("New file stored", ['path' => $value]);
                
                // Save the file path
                BrandingSetting::set($key, $value);
            }
        }

        // Then handle regular inputs
        foreach ($request->input('settings', []) as $key => $value) {
            $setting = $allSettings->get($key);
            
            if (!$setting) {
                continue;
            }

            // Skip file types (already handled above)
            if ($setting->type === 'file') {
                continue;
            }

            // Log color updates for debugging
            if ($setting->type === 'color' && $key === 'color_surface_dark') {
                \Log::info('Updating color_surface_dark', [
                    'old_value' => $setting->value,
                    'new_value' => $value,
                    'type' => $setting->type
                ]);
            }

            // Log text_shadow toggle updates for debugging
            if ($setting->type === 'boolean' && str_starts_with($key, 'text_shadow_')) {
                \Log::info("🔵 Updating {$key}", [
                    'old_value' => $setting->value,
                    'new_value' => $value,
                    'type' => $setting->type,
                    'value_type' => gettype($value)
                ]);
            }

            // Update setting (skip SEO Spanish fields, they'll be handled separately)
            if (!str_starts_with($key, 'seo_') || !str_ends_with($key, '_es')) {
                BrandingSetting::set($key, $value);
            }
        }

        // Handle all boolean settings (including unchecked ones)
        foreach ($allSettings->where('type', 'boolean') as $setting) {
            $value = $request->has("settings.{$setting->key}") ? '1' : '0';
            
            // Log text_shadow final values
            if (str_starts_with($setting->key, 'text_shadow_')) {
                \Log::info("🟢 FINAL {$setting->key}", [
                    'has_in_request' => $request->has("settings.{$setting->key}"),
                    'final_value' => $value
                ]);
            }
            
            BrandingSetting::set($setting->key, $value);
        }

        // Handle SEO translation (Spanish to other languages)
        $this->handleSeoTranslation($request);

        // Clear all caches
        BrandingSetting::clearCache();

        return back()->with('success', __('adminlte::adminlte.branding_updated_successfully'));
    }

    /**
     * Handle SEO translation from Spanish to other languages
     */
    protected function handleSeoTranslation(Request $request): void
    {
        try {
            $settings = $request->input('settings', []);
            
            // Check if any Spanish SEO fields are present and have changed
            $hasChangedSeoFields = false;
            $spanishSeoFields = [];
            
            foreach ($settings as $key => $value) {
                if (str_starts_with($key, 'seo_') && str_ends_with($key, '_es') && !empty($value)) {
                    $currentValue = BrandingSetting::get($key);
                    
                    // Only translate if the value has changed
                    if ($currentValue !== $value) {
                        $hasChangedSeoFields = true;
                        $spanishSeoFields[$key] = $value;
                        // Save the new Spanish value
                        BrandingSetting::set($key, $value);
                    }
                }
            }
            
            // Only run translation if there are actual changes
            if (!$hasChangedSeoFields) {
                \Log::info('SEO translation skipped: No Spanish SEO fields changed');
                return;
            }
            
            \Log::info('SEO fields changed, initiating translation', ['fields' => array_keys($spanishSeoFields)]);
            
            // Translate to other languages
            $seoService = app(\App\Services\SeoTranslationService::class);
            $translatedCount = $seoService->translateAllSeoFields($settings);

            if ($translatedCount > 0) {
                \Log::info("SEO translation completed: {$translatedCount} fields translated");
            }
        } catch (\Exception $e) {
            // Log error but don't fail the entire save
            \Log::error('SEO translation failed: ' . $e->getMessage());
        }
    }

    /**
     * Get the type for a given key
     */
    protected function getTypeForKey(string $key): string
    {
        $types = [
            'logo_main' => 'file',
            'logo_adminlte' => 'file',
            'favicon' => 'file',
            'background_image' => 'file',
            'hero_image' => 'file',
            'hero_enabled' => 'boolean',
            'background_enabled' => 'boolean',
            'text_shadow_enabled' => 'boolean',
            'text_shadow_headings' => 'boolean',
            'text_shadow_big_title' => 'boolean',
            'text_shadow_lead' => 'boolean',
            'text_shadow_text_muted' => 'boolean',
            'text_shadow_breadcrumbs' => 'boolean',
            'header_opacity' => 'number',
            'header_blur' => 'number',
            'footer_opacity' => 'number',
            'background_opacity' => 'number',
            'text_shadow_x' => 'number',
            'text_shadow_y' => 'number',
            'text_shadow_blur' => 'number',
            'text_shadow_opacity' => 'number',
        ];

        return $types[$key] ?? 'color';
    }

    /**
     * Get the category for a given key
     */
    protected function getCategoryForKey(string $key): string
    {
        if (str_starts_with($key, 'logo_') || $key === 'favicon') {
            return 'logos';
        }

        if (str_starts_with($key, 'hero_')) {
            return 'hero';
        }

        if (str_starts_with($key, 'seo_')) {
            return 'seo';
        }

        if (str_starts_with($key, 'reviews_embed_')) {
            return 'reviews_embed';
        }

        if (str_starts_with($key, 'text_shadow_')) {
            return 'text_shadow';
        }

        if (in_array($key, ['background_image', 'background_enabled'])) {
            return 'layout';
        }

        if (in_array($key, ['header_opacity', 'header_blur', 'footer_opacity', 'background_opacity', 'text_shadow_enabled'])) {
            return 'effects';
        }

        return 'colors';
    }

    /**
     * Save current branding as a template
     */
    public function saveTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $template = \App\Models\BrandingTemplate::createFromCurrent(
            $request->input('name'),
            $request->input('description')
        );

        return back()->with('success', "Template '{$template->name}' saved successfully!");
    }

    /**
     * Export template as JSON download
     */
    public function exportTemplate($id)
    {
        $template = \App\Models\BrandingTemplate::findOrFail($id);
        $json = $template->export();

        $filename = str_replace(' ', '_', strtolower($template->name)) . '_template.json';

        return response($json)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Export current branding configuration as JSON download
     */
    public function exportCurrentConfig()
    {
        \Log::info('=== EXPORT CURRENT CONFIG STARTED ===');
        
        try {
            // Get all branding settings
            $settings = BrandingSetting::all()->pluck('value', 'key')->toArray();
            \Log::info('Settings retrieved', ['count' => count($settings)]);

            // Create a temporary export structure
            $export = [
                'name' => 'Current Configuration',
                'description' => 'Exported on ' . now()->format('Y-m-d H:i:s'),
                'settings' => $settings,
                'exported_at' => now()->toIso8601String(),
            ];
            \Log::info('Export structure created', ['export_keys' => array_keys($export)]);

            $json = json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            \Log::info('JSON encoded', ['json_length' => strlen($json)]);
            
            $filename = 'branding_config_' . now()->format('Y-m-d_His') . '.json';
            \Log::info('Filename generated', ['filename' => $filename]);

            \Log::info('=== EXPORT CURRENT CONFIG COMPLETED - Returning response ===');
            
            return response($json)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
                
        } catch (\Exception $e) {
            \Log::error('=== EXPORT CURRENT CONFIG FAILED ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to export configuration: ' . $e->getMessage());
        }
    }

    /**
     * Import and apply configuration from JSON file
     */
    public function importTemplate(Request $request)
    {
        \Log::info('=== IMPORT TEMPLATE STARTED ===');
        
        $validator = Validator::make($request->all(), [
            'template_file' => 'required|file|mimes:json',
        ]);

        if ($validator->fails()) {
            \Log::warning('Import validation failed', ['errors' => $validator->errors()->toArray()]);
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Read JSON file
            $json = file_get_contents($request->file('template_file')->getRealPath());
            \Log::info('JSON file read', ['size' => strlen($json)]);
            
            $data = json_decode($json, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::error('JSON decode failed', ['error' => json_last_error_msg()]);
                return back()->with('error', 'Invalid JSON file: ' . json_last_error_msg());
            }
            
            \Log::info('JSON decoded successfully', ['keys' => array_keys($data)]);
            
            // Extract settings from the JSON structure
            $settings = $data['settings'] ?? $data;
            \Log::info('Settings extracted', ['count' => count($settings)]);
            
            // Apply each setting to the branding_settings table
            $updated = 0;
            $created = 0;
            
            foreach ($settings as $key => $value) {
                $setting = BrandingSetting::where('key', $key)->first();
                
                if ($setting) {
                    $setting->value = $value;
                    $setting->save();
                    $updated++;
                } else {
                    BrandingSetting::create([
                        'key' => $key,
                        'value' => $value,
                    ]);
                    $created++;
                }
            }
            
            \Log::info('=== IMPORT TEMPLATE COMPLETED ===', [
                'updated' => $updated,
                'created' => $created,
                'total' => count($settings)
            ]);
            
            $configName = $data['name'] ?? 'Configuration';
            return back()->with('success', "Configuration '{$configName}' imported and applied successfully! ({$updated} updated, {$created} created)");
            
        } catch (\Exception $e) {
            \Log::error('=== IMPORT TEMPLATE FAILED ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to import configuration: ' . $e->getMessage());
        }
    }

    /**
     * Apply a template to current branding
     */
    public function applyTemplate($id)
    {
        $template = \App\Models\BrandingTemplate::findOrFail($id);
        $template->apply();

        return back()->with('success', "Template '{$template->name}' applied successfully!");
    }

    /**
     * List all templates
     */
    public function templates()
    {
        $templates = \App\Models\BrandingTemplate::orderBy('created_at', 'desc')->get();
        return view('admin.branding.templates', compact('templates'));
    }

    /**
     * Delete a template
     */
    public function deleteTemplate($id)
    {
        $template = \App\Models\BrandingTemplate::findOrFail($id);
        $name = $template->name;
        $template->delete();

        return back()->with('success', "Template '{$name}' deleted successfully!");
    }
}
