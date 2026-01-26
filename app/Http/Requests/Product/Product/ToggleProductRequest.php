<?php

namespace App\Http\Requests\Product\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;

class ToggleProductRequest extends FormRequest
{
    protected string $controller = 'ProductController';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * No necesitamos campos obligatorios para el toggle,
     * pero dejamos el hook de validación y logging por consistencia.
     */
    public function rules(): array
    {
        return [
            // Si en el futuro quieres pasar un motivo:
            // 'reason' => ['nullable','string','max:255'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        LoggerHelper::validationFailed($this->controller, 'toggle', $validator->errors()->toArray(), [
            'entity'  => 'tour',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);

        parent::failedValidation($validator);
    }
}
