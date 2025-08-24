<?php

namespace App\Http\Requests\Tour\Amenity;

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

    protected function prepareForValidation(): void
    {
        $name = (string) $this->string('name')->trim()->squish();

        $this->merge([
            'name' => $name,
        ]);
    }

    public function rules(): array
    {
        /** @var Amenity|null $amenity */
        $amenity = $this->route('amenity');

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('amenities', 'name')->ignore($amenity?->amenity_id, 'amenity_id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la amenidad es obligatorio.',
            'name.string'   => 'El nombre debe ser texto.',
            'name.max'      => 'El nombre no puede superar 255 caracteres.',
            'name.unique'   => 'Ya existe una amenidad con ese nombre.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        /** @var Amenity|null $amenity */
        $amenity = $this->route('amenity');

        LoggerHelper::validationFailed($this->controller, 'update', $validator->errors()->toArray(), [
            'entity'    => 'amenity',
            'entity_id' => $amenity?->amenity_id,
            'user_id'   => optional($this->user())->getAuthIdentifier(),
        ]);

        parent::failedValidation($validator);
    }
}
