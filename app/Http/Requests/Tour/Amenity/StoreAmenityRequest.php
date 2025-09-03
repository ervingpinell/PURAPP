<?php

namespace App\Http\Requests\Tour\Amenity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use App\Services\LoggerHelper;

class StoreAmenityRequest extends FormRequest
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
        return [
            'name' => ['bail', 'required', 'string', 'max:255', Rule::unique('amenities', 'name')],
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

    public function attributes(): array
    {
        return [
            'name' => 'Nombre de la amenidad',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $action = $this->route()?->getActionMethod() ?? 'store';

        LoggerHelper::validationFailed($this->controller, $action, $validator->errors()->toArray(), [
            'entity'  => 'amenity',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);

        parent::failedValidation($validator);
    }
}
