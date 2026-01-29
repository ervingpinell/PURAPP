<?php

namespace App\Http\Requests\Product\ProductExcludedDate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;

class StoreMultipleExcludedDatesRequest extends FormRequest
{
    protected string $controller = 'ProductExcludedDateController';

    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $items = $this->input('items', []);
        if (!is_array($items)) $items = [];

        $normalized = array_map(function ($it) {
            return [
                'product_id'     => isset($it['product_id']) ? (int) $it['product_id'] : null,
                'schedule_id' => isset($it['schedule_id']) ? (int) $it['schedule_id'] : null,
                'date'        => isset($it['date']) ? (string) str($it['date'])->trim() : null,
            ];
        }, $items);

        $this->merge([
            'items'  => $normalized,
            'reason' => (string) $this->string('reason')->trim()->squish(),
        ]);
    }

    public function rules(): array
    {
        return [
            'items'               => ['required', 'array', 'min:1'],
            'items.*.product_id'     => ['required', 'exists:product2,product_id'],
            'items.*.schedule_id' => ['required', 'exists:schedules,schedule_id'],
            'items.*.date'        => ['required', 'date'],
            'reason'              => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'            => 'Debes enviar al menos un elemento.',
            'items.array'               => 'El formato de ítems es inválido.',
            'items.min'                 => 'Debes enviar al menos un elemento.',
            'items.*.product_id.required'  => 'El product es obligatorio.',
            'items.*.product_id.exists'    => 'Algún product no existe.',
            'items.*.schedule_id.required' => 'El horario es obligatorio.',
            'items.*.schedule_id.exists'   => 'Algún horario no existe.',
            'items.*.date.required'     => 'La fecha es obligatoria.',
            'items.*.date.date'         => 'Alguna fecha no es válida.',
            'reason.string'             => 'El motivo debe ser texto.',
            'reason.max'                => 'El motivo no puede superar 255 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        LoggerHelper::validationFailed($this->controller, 'storeMultiple', $validator->errors()->toArray(), [
            'entity'  => 'product_excluded_date',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }
}
