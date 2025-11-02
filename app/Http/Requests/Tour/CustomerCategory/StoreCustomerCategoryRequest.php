<?php

namespace App\Http\Requests\Tour\CustomerCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // O verificar permisos específicos
    }

    public function rules(): array
    {
        return [
            'slug'      => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9_-]+$/',
                Rule::unique('customer_categories', 'slug')
                    ->ignore($this->route('category')?->category_id, 'category_id'),
            ],
            'name'      => 'required|string|max:100',
            'age_from'  => 'required|integer|min:0|max:255',
            'age_to'    => 'nullable|integer|min:0|max:255|gte:age_from',
            'order'     => 'required|integer|min:0|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'age_to.gte' => 'La edad hasta debe ser mayor o igual que la edad desde.',
            'slug.regex' => 'El slug solo puede contener letras minúsculas, números, guiones y guiones bajos.',
        ];
    }

    /**
     * Validación adicional después de las reglas básicas
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $category = new \App\Models\CustomerCategory($this->validated());

            if ($this->route('category')) {
                $category->category_id = $this->route('category')->category_id;
            }

            if (!$category->validateNoOverlap()) {
                $validator->errors()->add(
                    'age_from',
                    'Los rangos de edad no pueden solaparse con otras categorías existentes.'
                );
            }
        });
    }
}
