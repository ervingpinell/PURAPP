<?php

namespace App\Http\Requests\Product\ProductType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;
use App\Models\ProductType;

class ToggleProductTypeRequest extends FormRequest
{
    protected string $controller = 'ProductTypeController';

    public function authorize(): bool
    {
        return true;
    }

    // No inputs a validar; pero si en el futuro agregas "reason", lo normalizas aquí.
    protected function prepareForValidation(): void
    {
        // noop
    }

    public function rules(): array
    {
        return [];
    }

    protected function failedValidation(Validator $validator)
    {
        /** @var ProductType|null $productType */
        $productType = $this->route('productType');

        LoggerHelper::validationFailed($this->controller, 'toggle', $validator->errors()->toArray(), [
            'entity'    => 'product_type',
            'entity_id' => $productType?->getKey(),
            'user_id'   => optional($this->user())->getAuthIdentifier(),
        ]);

        parent::failedValidation($validator);
    }
}
