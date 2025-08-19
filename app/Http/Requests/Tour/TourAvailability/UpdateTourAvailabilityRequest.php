<?php

namespace App\Http\Requests\Tour\TourAvailability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;
use App\Models\TourAvailability;

class UpdateTourAvailabilityRequest extends FormRequest
{
    protected string $controller = 'TourAvailabilityController';

    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tour_id'    => (int) $this->input('tour_id'),
            'date'       => (string) $this->string('date')->trim(),
            'start_time' => (string) $this->string('start_time')->trim(),
            'end_time'   => (string) $this->string('end_time')->trim(),
            'available'  => $this->has('available') ? $this->boolean('available') : null,
            'is_active'  => $this->has('is_active') ? $this->boolean('is_active') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'tour_id'    => ['required', 'exists:tours,tour_id'],
            'date'       => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time'   => ['nullable', 'date_format:H:i', 'after_or_equal:start_time'],
            'available'  => ['sometimes', 'boolean'],
            'is_active'  => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'tour_id.required'   => 'El tour es obligatorio.',
            'tour_id.exists'     => 'El tour seleccionado no existe.',
            'date.required'      => 'La fecha es obligatoria.',
            'date.date'          => 'La fecha no tiene un formato válido.',
            'start_time.date_format' => 'La hora de inicio debe tener el formato HH:MM (24h).',
            'end_time.date_format'   => 'La hora de fin debe tener el formato HH:MM (24h).',
            'end_time.after_or_equal'=> 'La hora de fin debe ser mayor o igual a la hora de inicio.',
            'available.boolean'  => 'El campo disponibilidad es inválido.',
            'is_active.boolean'  => 'El estado es inválido.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        /** @var TourAvailability|null $availability */
        $availability = $this->route('availability');

        LoggerHelper::validationFailed($this->controller, 'update', $validator->errors()->toArray(), [
            'entity'    => 'tour_availability',
            'entity_id' => $availability?->id,
            'user_id'   => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }
}
