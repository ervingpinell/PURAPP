<?php

namespace App\Http\Requests\Product\Schedule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;
use App\Models\Schedule;

class UpdateScheduleRequest extends FormRequest
{
    protected string $controller = 'ProductScheduleController';

    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'start_time' => $this->normalizeTime($this->input('start_time')),
            'end_time'   => $this->normalizeTime($this->input('end_time')),
            'label'      => (string) $this->string('label')->trim()->squish(),
            'is_active'  => $this->has('is_active') ? $this->boolean('is_active') : false, // checkbox
        ]);
    }

    public function rules(): array
    {
        return [
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
            'label'      => ['nullable', 'string', 'max:255'],
            'is_active'  => ['nullable', 'boolean'],
            // ELIMINADO: max_capacity ya no existe en schedules
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.required'    => 'El campo "Inicio" es obligatorio.',
            'start_time.date_format' => 'El campo "Inicio" debe tener el formato HH:MM (24h).',
            'end_time.required'      => 'El campo "Fin" es obligatorio.',
            'end_time.date_format'   => 'El campo "Fin" debe tener el formato HH:MM (24h).',
            'end_time.after'         => 'El campo "Fin" debe ser posterior al campo "Inicio".',
            'label.string'           => 'La etiqueta debe ser texto.',
            'label.max'              => 'La etiqueta no puede superar 255 caracteres.',
            'is_active.boolean'      => 'El estado debe ser verdadero o falso.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        /** @var Schedule|null $schedule */
        $schedule = $this->route('schedule');

        LoggerHelper::validationFailed($this->controller, 'update', $validator->errors()->toArray(), [
            'entity'    => 'schedule',
            'entity_id' => $schedule?->getKey(),
            'user_id'   => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }

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
