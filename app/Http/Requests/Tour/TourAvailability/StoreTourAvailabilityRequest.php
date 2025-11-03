<?php

namespace App\Http\Requests\Tour\TourAvailability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;

class StoreTourAvailabilityRequest extends FormRequest
{
    protected string $controller = 'TourAvailabilityController';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tour_id'      => $this->filled('tour_id') ? (int) $this->input('tour_id') : null,
            'schedule_id'  => $this->filled('schedule_id') ? (int) $this->input('schedule_id') : null,
            'date'         => $this->filled('date') ? (string) $this->string('date')->trim() : null,
            'max_capacity' => $this->filled('max_capacity') ? (int) $this->input('max_capacity') : null,
            'is_blocked'   => $this->has('is_blocked') ? $this->boolean('is_blocked') : false,
        ]);
    }

    public function rules(): array
    {
        return [
            'tour_id'      => ['required', 'integer', 'exists:tours,tour_id'],
            'schedule_id'  => ['required', 'integer', 'exists:schedules,schedule_id'],
            'date'         => ['required', 'date', 'after_or_equal:today'],
            'max_capacity' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_blocked'   => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'tour_id.required'           => 'El tour es obligatorio.',
            'tour_id.integer'            => 'El tour debe ser un número válido.',
            'tour_id.exists'             => 'El tour seleccionado no existe.',
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
            'tour_id'      => 'Tour',
            'schedule_id'  => 'Horario',
            'date'         => 'Fecha',
            'max_capacity' => 'Capacidad',
            'is_blocked'   => 'Bloqueado',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        LoggerHelper::validationFailed($this->controller, 'store', $validator->errors()->toArray(), [
            'entity'  => 'tour_availability',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }
}
