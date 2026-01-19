<?php
// app/Http/Requests/Tour/CustomerCategory/StoreCustomerCategoryRequest.php
namespace App\Http\Requests\Tour\CustomerCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('category')?->category_id;

        $rules = [
            'slug'      => [
                'required','alpha_dash','max:60',
                Rule::unique('customer_categories','slug')->ignore($id, 'category_id'),
            ],
            'age_from'  => ['required','integer','min:0'],
            'age_to'    => ['nullable','integer','min:0','gte:age_from'],
            'order'     => ['nullable','integer','min:0'],
            'is_active' => ['sometimes','boolean'],

            // Traducciones
            'names'     => ['required','array'],
        ];

        // Al menos el idioma por defecto (fallback) debe venir
        $first = config('app.fallback_locale', 'es');
        $rules["names.$first"] = ['required','string','max:120'];

        // El resto opcionales
        foreach (supported_locales() as $loc) {
            if ($loc === $first) continue;
            $rules["names.$loc"] = ['nullable','string','max:120'];
        }

        // Flags de auto-traducción opcionales
        $rules['auto_translate'] = ['sometimes','boolean'];
        $rules['regen_missing']  = ['sometimes','boolean'];

        return $rules;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => (bool) $this->boolean('is_active'),
            'order'     => $this->input('order', 0),
        ]);

        // Compatibility for inline creation dealing with single input
        if (!$this->has('names') && $this->input('initial_name')) {
            $fallback = config('app.fallback_locale', 'es');
            $this->merge([
                'names' => [
                    $fallback => $this->input('initial_name')
                ]
            ]);
        }
    }
}
