<?php

namespace App\Http\Requests\Reviews;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\ReviewProvider;

class UpsertProviderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('provider')
            ? ($this->user()?->can('edit-review-providers') ?? false)
            : ($this->user()?->can('create-review-providers') ?? false);
    }

    /**
     * Normaliza:
     * - settings: string JSON -> array.
     * - slug: minúsculas/trim.
     * - booleans: indexable, is_active.
     */
    protected function prepareForValidation(): void
    {
        $settings = $this->input('settings');

        if (is_string($settings)) {
            $decoded = null;
            try {
                $decoded = json_decode($settings, true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) {
                // Dejará fallar la validación 'array' si era inválido
            }
            if (is_array($decoded)) {
                $settings = $decoded;
            }
        }

        $slug = $this->input('slug');
        if (is_string($slug)) {
            $slug = strtolower(trim($slug));
        }

        $this->merge([
            'slug'          => $slug,
            'settings'      => $settings,
            'indexable'     => filter_var($this->input('indexable'), FILTER_VALIDATE_BOOLEAN),
            'is_active'     => filter_var($this->input('is_active', true), FILTER_VALIDATE_BOOLEAN),
            'cache_ttl_sec' => $this->input('cache_ttl_sec'),
        ]);
    }

    public function rules(): array
    {
        $provider   = $this->route('provider'); // modelo en update, null en create
        $providerId = is_object($provider) ? ($provider->id ?? null) : (is_numeric($provider) ? (int)$provider : null);

        return [
            'name'          => ['required', 'string', 'max:100'],
            'slug'          => [
                'required',
                'string',
                'max:100',
                'alpha_dash:ascii',
                Rule::unique('review_providers', 'slug')->ignore($providerId),
            ],
            // driver lo controla el controlador; aquí no es crítico
            'driver'        => ['sometimes', 'string', 'max:150'],

            'indexable'     => ['required', 'boolean'],
            'is_active'     => ['required', 'boolean'],
            'cache_ttl_sec' => ['nullable', 'integer', 'min:60', 'max:1209600'], // 1 min .. 14 días

            // Para externos (http_json). Si viene como string inválido, fallará aquí.
            'settings'      => ['nullable', 'array'],

            // Para local (usado por el controller); si viene, validarlo.
            'min_stars'     => ['nullable', 'integer', 'min:0', 'max:5'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.alpha_dash'   => 'El slug solo puede contener letras, números, guiones y guiones bajos.',
            'slug.unique'       => 'Ese slug ya está en uso.',
            'settings.array'    => 'Settings debe ser un objeto/array. Si pegas JSON, asegúrate de que sea válido.',
            'cache_ttl_sec.min' => 'El TTL mínimo es 60 segundos.',
        ];
    }
}
