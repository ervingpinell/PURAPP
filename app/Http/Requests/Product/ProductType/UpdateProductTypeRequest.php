<?php

namespace App\Http\Requests\Product\ProductType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use App\Services\LoggerHelper;
use App\Models\ProductType;

class UpdateProductTypeRequest extends FormRequest
{
    protected string $controller = 'ProductTypeController';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $name        = (string) $this->string('name')->trim()->squish();
        $description = (string) $this->string('description')->trim();
        $duration    = (string) $this->string('duration')->trim()->squish();

        $this->merge([
            'name'        => $name,
            'description' => $description === '' ? null : $description,
            'duration'    => $duration === '' ? null : $duration,
        ]);
    }

    public function rules(): array
    {
        /** @var ProductType|null $productType */
        $productType = $this->route('productType');

        $rules = [
            'description' => ['nullable', 'string', 'max:1000'],
            'duration'    => ['nullable', 'string', 'max:255'],
        ];

        if ($this->has('translations')) {
             $rules['translations'] = ['required', 'array'];
             $rules['translations.*.name'] = ['required', 'string', 'max:255'];
             // You can add more specific rules here if needed
        } else {
            // Obtener el ID de la traducción en español para ignorarla en la validación de unicidad
            $translationId = $productType?->translations()->where('locale', 'es')->first()?->id;

            $rules['name'] = [
                'required',
                'string',
                'max:255',
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required'   => 'El nombre es obligatorio.',
            'name.string'     => 'El nombre debe ser texto.',
            'name.max'        => 'El nombre no puede superar 255 caracteres.',
            'name.unique'     => 'Ya existe un tipo de producto con ese nombre.',
            'description.max' => 'La descripción no puede superar 1000 caracteres.',
            'duration.max'    => 'La duración no puede superar 255 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        /** @var ProductType|null $productType */
        $productType = $this->route('productType');

        LoggerHelper::validationFailed($this->controller, 'update', $validator->errors()->toArray(), [
            'entity'    => 'product_type',
            'entity_id' => $productType?->getKey(),
            'user_id'   => optional($this->user())->getAuthIdentifier(),
        ]);

        parent::failedValidation($validator);
    }
}
