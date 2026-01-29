<?php

namespace App\Http\Requests\Product\ProductAvailability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;
use App\Models\ProductAvailability;

class UpdateProductAvailabilityRequest extends FormRequest
{
    protected string $controller = 'ProductAvailabilityController';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'max_capacity' => $this->filled('max_capacity') ? (int) $this->input('max_capacity') : null,
            'is_blocked'   => $this->has('is_blocked') ? $this->boolean('is_blocked') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'max_capacity' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_blocked'   => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'max_capacity.integer' => 'La capacidad debe ser un número entero.',
            'max_capacity.min'     => 'La capacidad debe ser al menos 0.',
            'max_capacity.max'     => 'La capacidad no puede superar 999.',
            'is_blocked.boolean'   => 'El estado de bloqueo debe ser verdadero o falso.',
        ];
    }

    public function attributes(): array
    {
        return [
            'max_capacity' => 'Capacidad',
            'is_blocked'   => 'Bloqueado',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        /** @var ProductAvailability|null $availability */
        $availability = $this->route('availability');

        LoggerHelper::validationFailed($this->controller, 'update', $validator->errors()->toArray(), [
            'entity'    => 'tour_availability',
            'entity_id' => $availability?->getKey(),
            'user_id'   => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }
}
