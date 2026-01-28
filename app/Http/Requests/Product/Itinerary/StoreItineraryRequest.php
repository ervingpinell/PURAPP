<?php

namespace App\Http\Requests\Product\Itinerary;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use App\Services\LoggerHelper;

class StoreItineraryRequest extends FormRequest
{
    protected string $controller = 'ItineraryController';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'        => $this->string('name')->trim()->toString(),
            'description' => $this->filled('description')
                ? $this->string('description')->trim()->toString()
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name'        => ['bail','required','string','max:255'],
            'description' => ['nullable','string','max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => __('m_tours.itinerary.validation.name.required'),
            'name.string'        => __('m_tours.itinerary.validation.name.string'),
            'name.max'           => __('m_tours.itinerary.validation.name.max'),
            'name.unique'        => __('m_tours.itinerary.validation.name.unique'),
            'description.string' => __('m_tours.itinerary.validation.description.string'),
            'description.max'    => __('m_tours.itinerary.validation.description.max'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name'        => __('m_tours.itinerary.fields.name'),
            'description' => __('m_tours.itinerary.fields.description'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $action = $this->route()?->getActionMethod() ?? 'store';

        LoggerHelper::validationFailed($this->controller, $action, $validator->errors()->toArray(), [
            'entity'  => 'itinerary',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);

        parent::failedValidation($validator);
    }
}
