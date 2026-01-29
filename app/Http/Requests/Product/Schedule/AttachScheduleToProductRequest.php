<?php

namespace App\Http\Requests\Product\Schedule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;

class AttachScheduleToProductRequest extends FormRequest
{
    protected string $controller = 'ProductScheduleController';

    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'schedule_id'   => $this->filled('schedule_id') ? (int) $this->input('schedule_id') : null,
            'base_capacity' => $this->filled('base_capacity') ? (int) $this->input('base_capacity') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'schedule_id'   => ['required', 'exists:schedules,schedule_id'],
            'base_capacity' => ['nullable', 'integer', 'min:1', 'max:999'], // NUEVO
        ];
    }

    public function messages(): array
    {
        return [
            'schedule_id.required'  => 'Debes seleccionar un horario.',
            'schedule_id.exists'    => 'El horario seleccionado no existe.',
            'base_capacity.integer' => 'La capacidad override debe ser un número entero.',
            'base_capacity.min'     => 'La capacidad override debe ser al menos 1.',
            'base_capacity.max'     => 'La capacidad override no puede superar 999.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        LoggerHelper::validationFailed($this->controller, 'attach', $validator->errors()->toArray(), [
            'entity'  => 'product_schedule_pivot',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }
}
