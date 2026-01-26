<?php

namespace App\Http\Requests\Product\ProductExcludedDate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;

class DestroySelectedExcludedDatesRequest extends FormRequest
{
    protected string $controller = 'ProductExcludedDateController';

    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $ids = $this->input('ids', []);
        if (!is_array($ids)) $ids = [];

        $ids = array_values(array_filter(array_map('intval', $ids), fn($v) => $v > 0));

        $this->merge(['ids' => $ids]);
    }

    public function rules(): array
    {
        return [
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:tour_excluded_dates,tour_excluded_date_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Debe seleccionar al menos un elemento.',
            'ids.array'    => 'El formato de selección es inválido.',
            'ids.min'      => 'Debe seleccionar al menos un elemento.',
            'ids.*.exists' => 'Alguna fecha seleccionada no existe.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        LoggerHelper::validationFailed($this->controller, 'destroySelected', $validator->errors()->toArray(), [
            'entity'  => 'tour_excluded_date',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }
}
