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
        $settings = BrandingSetting::all()->groupBy('category');
        
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

        foreach ($request->input('settings', []) as $key => $value) {
            $setting = BrandingSetting::where('key', $key)->first();
            
            if (!$setting) {
                continue;
            }

            // Handle file uploads
            if ($setting->type === 'file' && $request->hasFile("settings.{$key}")) {
                $file = $request->file("settings.{$key}");
                
                // Delete old file if exists
                if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                    Storage::disk('public')->delete($setting->value);
                }
                
                // Store new file
                $path = $file->store('branding', 'public');
                $value = '/storage/' . $path;
            }

            // Handle boolean values
            if ($setting->type === 'boolean') {
                $value = $request->has("settings.{$key}") ? '1' : '0';
            }

            // Update setting
            BrandingSetting::set($key, $value);
        }

        // Clear all caches
        BrandingSetting::clearCache();

        return back()->with('success', __('adminlte::adminlte.branding_updated_successfully'));
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
            'hero_enabled' => 'boolean',
            'background_enabled' => 'boolean',
            'text_shadow_enabled' => 'boolean',
            'header_opacity' => 'number',
            'header_blur' => 'number',
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

        if (in_array($key, ['hero_enabled', 'background_image', 'background_enabled'])) {
            return 'layout';
        }

        if (in_array($key, ['header_opacity', 'header_blur', 'text_shadow_enabled'])) {
            return 'effects';
        }

        return 'colors';
    }
}
