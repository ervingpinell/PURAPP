<?php

namespace App\Http\Requests\Product\ProductExcludedDate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;

class BlockAllRequest extends FormRequest
{
    protected string $controller = 'ProductExcludedDateController';

    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'start_date' => (string) $this->string('start_date')->trim(),
            'end_date'   => (string) $this->string('end_date')->trim(),
            'reason'     => (string) $this->string('reason')->trim()->squish(),
        ]);
    }

    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
            'reason'     => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required' => 'La fecha inicial es obligatoria.',
            'start_date.date'     => 'La fecha inicial no es válida.',
            'end_date.date'       => 'La fecha final no es válida.',
            'end_date.after_or_equal' => 'La fecha final debe ser igual o posterior a la inicial.',
            'reason.string'       => 'El motivo debe ser texto.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        LoggerHelper::validationFailed($this->controller, 'blockAll', $validator->errors()->toArray(), [
            'entity'  => 'product_excluded_date',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }
}
