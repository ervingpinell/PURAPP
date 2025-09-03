<?php

namespace App\Http\Requests\Tour\ItineraryItem;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;

class StoreItineraryItemRequest extends FormRequest
{
    protected string $controller = 'ItineraryItemController';

    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $title       = (string) $this->string('title')->trim()->squish();
        $description = (string) $this->string('description')->trim();

        $this->merge([
            'title'       => $title,
            'description' => $description,
        ]);
    }

    public function rules(): array
    {
        return [
            'title'       => ['bail','required','string','max:255'],
            'description' => ['bail','required','string','max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => __('m_tours.itinerary_item.validation.title.required'),
            'title.string'         => __('m_tours.itinerary_item.validation.title.string'),
            'title.max'            => __('m_tours.itinerary_item.validation.title.max'),
            'description.required' => __('m_tours.itinerary_item.validation.description.required'),
            'description.string'   => __('m_tours.itinerary_item.validation.description.string'),
            'description.max'      => __('m_tours.itinerary_item.validation.description.max'),
        ];
    }

    public function attributes(): array
    {
        return [
            'title'       => __('m_tours.itinerary_item.fields.title'),
            'description' => __('m_tours.itinerary_item.fields.description'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $action = $this->route()?->getActionMethod() ?? 'store';

        LoggerHelper::validationFailed($this->controller, $action, $validator->errors()->toArray(), [
            'entity'  => 'itinerary_item',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);

        parent::failedValidation($validator);
    }
}
