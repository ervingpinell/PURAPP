<?php

namespace App\Http\Requests\Tour\TourType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;
use App\Models\TourType;

class ToggleTourTypeRequest extends FormRequest
{
    protected string $controller = 'TourTypeController';

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
        /** @var TourType|null $tourType */
        $tourType = $this->route('tourType');

        LoggerHelper::validationFailed($this->controller, 'toggle', $validator->errors()->toArray(), [
            'entity'    => 'tour_type',
            'entity_id' => $tourType?->getKey(),
            'user_id'   => optional($this->user())->getAuthIdentifier(),
        ]);

        parent::failedValidation($validator);
    }
}
