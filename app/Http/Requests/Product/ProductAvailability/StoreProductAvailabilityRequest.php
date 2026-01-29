<?php

namespace App\Http\Requests\Product\ProductAvailability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;

class StoreProductAvailabilityRequest extends FormRequest
{
    protected string $controller = 'ProductAvailabilityController';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'product_id'      => $this->filled('product_id') ? (int) $this->input('product_id') : null,
            'schedule_id'  => $this->filled('schedule_id') ? (int) $this->input('schedule_id') : null,
            'date'         => $this->filled('date') ? (string) $this->string('date')->trim() : null,
            'max_capacity' => $this->filled('max_capacity') ? (int) $this->input('max_capacity') : null,
            'is_blocked'   => $this->has('is_blocked') ? $this->boolean('is_blocked') : false,
        ]);
    }

    public function rules(): array
    {
        return [
            'product_id'      => ['required', 'integer', 'exists:product2,product_id'],
            'schedule_id'  => ['required', 'integer', 'exists:schedules,schedule_id'],
            'date'         => ['required', 'date', 'after_or_equal:today'],
            'max_capacity' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_blocked'   => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required'           => 'El product es obligatorio.',
            'product_id.integer'            => 'El product debe ser un número válido.',
            'product_id.exists'             => 'El product seleccionado no existe.',
            'schedule_id.required'       => 'El horario es obligatorio.',
            'schedule_id.integer'        => 'El horario debe ser un número válido.',
            'schedule_id.exists'         => 'El horario seleccionado no existe.',
            'date.required'              => 'La fecha es obligatoria.',
            'date.date'                  => 'La fecha debe ser válida.',
            'date.after_or_equal'        => 'La fecha debe ser hoy o posterior.',
            'max_capacity.integer'       => 'La capacidad debe ser un número entero.',
            'max_capacity.min'           => 'La capacidad debe ser al menos 0.',
            'max_capacity.max'           => 'La capacidad no puede superar 999.',
            'is_blocked.boolean'         => 'El estado de bloqueo debe ser verdadero o falso.',
        ];
    }

    public function attributes(): array
    {
        return [
            'product_id'      => 'Product',
            'schedule_id'  => 'Horario',
            'date'         => 'Fecha',
            'max_capacity' => 'Capacidad',
            'is_blocked'   => 'Bloqueado',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        LoggerHelper::validationFailed($this->controller, 'store', $validator->errors()->toArray(), [
            'entity'  => 'product_availability',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }
}
