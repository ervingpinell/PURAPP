<?php

namespace App\Http\Requests\Tour\TourExcludedDate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;

class BulkToggleExcludedDatesRequest extends FormRequest
{
    protected string $controller = 'TourExcludedDateController';

    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $items = $this->input('items', []);
        if (!is_array($items)) $items = [];

        $normalized = array_map(function ($it) {
            return [
                'tour_id'     => isset($it['tour_id']) ? (int) $it['tour_id'] : null,
                'schedule_id' => isset($it['schedule_id']) ? (int) $it['schedule_id'] : null,
                'date'        => isset($it['date']) ? (string) str($it['date'])->trim() : null,
            ];
        }, $items);

        $this->merge([
            'items'  => $normalized,
            'want'   => (string) $this->string('want')->trim(),
            'reason' => (string) $this->string('reason')->trim()->squish(),
        ]);
    }

    public function rules(): array
    {
        return [
            'items'               => ['required', 'array', 'min:1'],
            'items.*.tour_id'     => ['required', 'exists:tours,tour_id'],
            'items.*.schedule_id' => ['required', 'exists:schedules,schedule_id'],
            'items.*.date'        => ['required', 'date'],
            'want'                => ['nullable', 'in:block,unblock'],
            'reason'              => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'          => 'Debes enviar al menos un elemento.',
            'items.array'             => 'El formato de ítems es inválido.',
            'items.min'               => 'Debes enviar al menos un elemento.',
            'items.*.tour_id.required'=> 'El tour es obligatorio.',
            'items.*.tour_id.exists'  => 'Algún tour no existe.',
            'items.*.schedule_id.required'=> 'El horario es obligatorio.',
            'items.*.schedule_id.exists'  => 'Algún horario no existe.',
            'items.*.date.required'   => 'La fecha es obligatoria.',
            'items.*.date.date'       => 'Alguna fecha no es válida.',
            'want.in'                 => 'El valor de acción es inválido.',
            'reason.string'           => 'El motivo debe ser texto.',
            'reason.max'              => 'El motivo no puede superar 255 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        LoggerHelper::validationFailed($this->controller, 'bulkToggle', $validator->errors()->toArray(), [
            'entity'  => 'tour_availability',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }
}
