<?php

namespace App\Http\Requests\Tour\Schedule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;

class StoreScheduleRequest extends FormRequest
{
    protected string $controller = 'TourScheduleController';

    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tour_id'      => $this->filled('tour_id') ? (int) $this->input('tour_id') : null,
            'start_time'   => $this->normalizeTime($this->input('start_time')),
            'end_time'     => $this->normalizeTime($this->input('end_time')),
            'label'        => (string) $this->string('label')->trim()->squish(),
            'max_capacity' => $this->filled('max_capacity') ? (int) $this->input('max_capacity') : null,
            'is_active'    => $this->has('is_active') ? $this->boolean('is_active') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'tour_id'      => ['nullable', 'exists:tours,tour_id'],
            'start_time'   => ['required', 'date_format:H:i'],
            'end_time'     => ['required', 'date_format:H:i', 'after:start_time'],
            'label'        => ['nullable', 'string', 'max:255'],
            'max_capacity' => ['required', 'integer', 'min:1'],
            'is_active'    => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'tour_id.exists'        => 'El tour seleccionado no existe.',
            'start_time.required'   => 'El campo "Inicio" es obligatorio.',
            'start_time.date_format'=> 'El campo "Inicio" debe tener el formato HH:MM (24h).',
            'end_time.required'     => 'El campo "Fin" es obligatorio.',
            'end_time.date_format'  => 'El campo "Fin" debe tener el formato HH:MM (24h).',
            'end_time.after'        => 'El campo "Fin" debe ser posterior al campo "Inicio".',
            'label.string'          => 'La etiqueta debe ser texto.',
            'label.max'             => 'La etiqueta no puede superar 255 caracteres.',
            'max_capacity.required' => 'La capacidad máxima es obligatoria.',
            'max_capacity.integer'  => 'La capacidad máxima debe ser un número entero.',
            'max_capacity.min'      => 'La capacidad máxima debe ser al menos 1.',
            'is_active.boolean'     => 'El estado debe ser verdadero o falso.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        LoggerHelper::validationFailed($this->controller, 'store', $validator->errors()->toArray(), [
            'entity'  => 'schedule',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }

    /** Normaliza entradas de hora (e.g., "3:15 pm") a H:i */
    private function normalizeTime(?string $input): ?string
    {
        if (!$input) return null;
        $input = trim($input);

        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $input)) {
            return \DateTime::createFromFormat('H:i:s', $input)?->format('H:i') ?: null;
        }

        $candidates = [
            'H:i',
            'g:i a', 'g:iA', 'g:ia', 'g:i A',
            'g a', 'gA', 'ga', 'g A',
            'H:i \h',
        ];

        foreach ($candidates as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, strtolower($input));
            if ($dt !== false) {
                return $dt->format('H:i');
            }
        }
        return null;
    }
}
