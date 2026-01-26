<?php

namespace App\Http\Requests\Product\Schedule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;

class StoreScheduleRequest extends FormRequest
{
    protected string $controller = 'ProductScheduleController';

    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'product_id'       => $this->filled('product_id') ? (int) $this->input('product_id') : null,
            'start_time'    => $this->normalizeTime($this->input('start_time')),
            'end_time'      => $this->normalizeTime($this->input('end_time')),
            'label'         => (string) $this->string('label')->trim()->squish(),
            'base_capacity' => $this->filled('base_capacity') ? (int) $this->input('base_capacity') : null,
            'is_active'     => $this->has('is_active') ? $this->boolean('is_active') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'product_id'       => ['nullable', 'exists:tours,product_id'],
            'start_time'    => [
                'required',
                'date_format:H:i',
                \Illuminate\Validation\Rule::unique('schedules')->where(function ($query) {
                    return $query->where('end_time', $this->normalizeTime($this->input('end_time')));
                })
            ],
            'end_time'      => ['required', 'date_format:H:i', 'after:start_time'],
            'label'         => ['nullable', 'string', 'max:255'],
            'base_capacity' => ['nullable', 'integer', 'min:1', 'max:999'], // CAMBIADO: ahora es opcional
            'is_active'     => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.exists'          => 'El tour seleccionado no existe.',
            'start_time.required'     => 'El campo "Inicio" es obligatorio.',
            'start_time.date_format'  => 'El campo "Inicio" debe tener el formato HH:MM (24h).',
            'end_time.required'       => 'El campo "Fin" es obligatorio.',
            'end_time.date_format'    => 'El campo "Fin" debe tener el formato HH:MM (24h).',
            'end_time.after'          => 'El campo "Fin" debe ser posterior al campo "Inicio".',
            'label.string'            => 'La etiqueta debe ser texto.',
            'label.max'               => 'La etiqueta no puede superar 255 caracteres.',
            'base_capacity.integer'   => 'La capacidad override debe ser un número entero.',
            'base_capacity.min'       => 'La capacidad override debe ser al menos 1.',
            'base_capacity.max'       => 'La capacidad override no puede superar 999.',
            'is_active.boolean'       => 'El estado debe ser verdadero o falso.',
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
