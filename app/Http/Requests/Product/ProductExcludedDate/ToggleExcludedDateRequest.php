<?php

namespace App\Http\Requests\Product\ProductExcludedDate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;

class ToggleExcludedDateRequest extends FormRequest
{
    protected string $controller = 'ProductExcludedDateController';

    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'product_id'     => (int) $this->input('product_id'),
            'schedule_id' => (int) $this->input('schedule_id'),
            'date'        => (string) $this->string('date')->trim(),
            'want'        => (string) $this->string('want')->trim(),
            'reason'      => (string) $this->string('reason')->trim()->squish(),
        ]);
    }

    public function rules(): array
    {
        return [
            'product_id'     => ['required', 'exists:product2,product_id'],
            'schedule_id' => ['required', 'exists:schedules,schedule_id'],
            'date'        => ['required', 'date'],
            'want'        => ['nullable', 'in:block,unblock'],
            'reason'      => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required'     => 'El tour es obligatorio.',
            'product_id.exists'       => 'El tour seleccionado no existe.',
            'schedule_id.required' => 'El horario es obligatorio.',
            'schedule_id.exists'   => 'El horario seleccionado no existe.',
            'date.required'        => 'La fecha es obligatoria.',
            'date.date'            => 'La fecha no es válida.',
            'want.in'              => 'El valor de acción es inválido.',
            'reason.string'        => 'El motivo debe ser texto.',
            'reason.max'           => 'El motivo no puede superar 255 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        LoggerHelper::validationFailed($this->controller, 'toggle', $validator->errors()->toArray(), [
            'entity'  => 'tour_availability',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }
}
