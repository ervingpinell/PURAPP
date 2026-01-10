<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\LoggerHelper;

/**
 * SettingsController
 *
 * Handles settings operations.
 */
class SettingsController extends Controller
{
    /**
     * Display settings grouped by category
     */
    public function index()
    {
        $settings = Setting::orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        $categoryLabels = [
            'cart' => 'Carrito',
            'booking' => 'Reservas',
            'email' => 'Correo Electrónico',
            'payment' => 'Pagos',
            'general' => 'General',
        ];

        return view('admin.settings.index', compact('settings', 'categoryLabels'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $updated = 0;
        $errors = [];

        foreach ($request->input('settings', []) as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if (!$setting) {
                continue;
            }

            // Validate based on rules
            if ($setting->validation_rules && is_array($setting->validation_rules)) {
                try {
                    $request->validate([
                        "settings.{$key}" => $setting->validation_rules
                    ]);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    $errors[$setting->label] = $e->errors()["settings.{$key}"][0] ?? 'Error de validación';
                    continue;
                }
            }

            // Update setting
            $setting->update([
                'value' => $value,
                'updated_by' => auth()->id(),
            ]);

            // Clear cache
            Cache::forget("setting.{$key}");
            $updated++;
        }

        if (!empty($errors)) {
            return redirect()
                ->route('admin.settings.index')
                ->withErrors($errors)
                ->with('warning', "Se actualizaron {$updated} configuraciones, pero hubo errores en algunas.");
        }

        LoggerHelper::mutated('SettingsController', 'update', 'Setting', null, ['updated_count' => $updated]);

        return redirect()
            ->route('admin.settings.index')
            ->with('success', "Se actualizaron {$updated} configuraciones correctamente.");
    }
}
