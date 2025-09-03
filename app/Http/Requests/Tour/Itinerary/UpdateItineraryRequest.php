<?php

namespace App\Http\Requests\Tour\Itinerary;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use App\Services\LoggerHelper;

class UpdateItineraryRequest extends FormRequest
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
        $itinerary = $this->route('itinerary');
        $id        = $itinerary?->itinerary_id;

        return [
            'name' => [
                'bail','required','string','max:255',
                Rule::unique('itineraries','name')->ignore($id, 'itinerary_id'),
            ],
            'description' => ['nullable','string','max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => __('m_tours.itinerary.validation.name.required'),
            'name.string'       => __('m_tours.itinerary.validation.name.string'),
            'name.max'          => __('m_tours.itinerary.validation.name.max'),
            'name.unique'       => __('m_tours.itinerary.validation.name.unique'),
            'description.string'=> __('m_tours.itinerary.validation.description.string'),
            'description.max'   => __('m_tours.itinerary.validation.description.max'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $itinerary = $this->route('itinerary');
        $action    = $this->route()?->getActionMethod() ?? 'update';

        LoggerHelper::validationFailed($this->controller, $action, $validator->errors()->toArray(), [
            'entity'    => 'itinerary',
            'entity_id' => $itinerary?->itinerary_id,
            'user_id'   => optional($this->user())->getAuthIdentifier(),
        ]);

        parent::failedValidation($validator);
    }
}
