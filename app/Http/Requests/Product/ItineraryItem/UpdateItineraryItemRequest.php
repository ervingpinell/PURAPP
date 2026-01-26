<?php

namespace App\Http\Requests\Product\ItineraryItem;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;
use App\Models\ItineraryItem;

class UpdateItineraryItemRequest extends FormRequest
{
    protected string $controller = 'ItineraryItemController';

    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $title       = (string) $this->string('title')->trim()->squish();
        $description = (string) $this->string('description')->trim();
        $isActive    = $this->has('is_active') ? $this->boolean('is_active') : null;

        $this->merge([
            'title'       => $title,
            'description' => $description,
            'is_active'   => $isActive,
        ]);
    }

    public function rules(): array
    {
        return [
            'title'       => ['bail','required','string','max:255'],
            'description' => ['bail','required','string','max:2000'],
            'is_active'   => ['nullable','boolean'],
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
            'is_active.boolean'    => __('validation.boolean'),
        ];
    }

    public function attributes(): array
    {
        return [
            'title'       => __('m_tours.itinerary_item.fields.title'),
            'description' => __('m_tours.itinerary_item.fields.description'),
            'is_active'   => __('m_tours.itinerary_item.status.active'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        /** @var ItineraryItem|null $itineraryItem */
        $itineraryItem = $this->route('itinerary_item');
        $action        = $this->route()?->getActionMethod() ?? 'update';

        LoggerHelper::validationFailed($this->controller, $action, $validator->errors()->toArray(), [
            'entity'    => 'itinerary_item',
            'entity_id' => $itineraryItem?->item_id,
            'user_id'   => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }
}
