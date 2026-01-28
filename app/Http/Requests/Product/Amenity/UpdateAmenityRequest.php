<?php

namespace App\Http\Requests\Product\Amenity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use App\Services\LoggerHelper;
use App\Models\Amenity;

class UpdateAmenityRequest extends FormRequest
{
    protected string $controller = 'AmenityController';

    public function authorize(): bool
    {
        return true;
    }



    public function rules(): array
    {
        /** @var Amenity|null $amenity */
        $amenity = $this->route('amenity');
        $rules = [
            'translations' => ['required', 'array'],
        ];

        foreach (supported_locales() as $locale) {
            $rules["translations.$locale"] = [
                'bail',
                'required',
                'string',
                'max:255',
                // Note: Unique validation removed - Spatie Translatable stores in JSON
                // Manual uniqueness check can be added in controller if needed
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'translations.*.required' => 'El nombre de la amenidad es obligatorio para todos los idiomas.',
            'translations.*.string'   => 'El nombre debe ser texto.',
            'translations.*.max'      => 'El nombre no puede superar 255 caracteres.',
            'translations.*.unique'   => 'Ya existe una amenidad con ese nombre en ese idioma.',
        ];
    }

    public function attributes(): array
    {
        $attributes = [];
        foreach (supported_locales() as $locale) {
            $attributes["translations.$locale"] = "Nombre ($locale)";
        }
        return $attributes;
    }

    protected function failedValidation(Validator $validator)
    {
        /** @var Amenity|null $amenity */
        $amenity = $this->route('amenity');
        $action  = $this->route()?->getActionMethod() ?? 'update';

        LoggerHelper::validationFailed($this->controller, $action, $validator->errors()->toArray(), [
            'entity'    => 'amenity',
            'entity_id' => $amenity?->amenity_id,
            'user_id'   => optional($this->user())->getAuthIdentifier(),
        ]);

        parent::failedValidation($validator);
    }
}
