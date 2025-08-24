<?php

namespace App\Http\Requests\Tour\ItineraryItem;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;
use App\Models\ItineraryItem;

class ToggleItineraryItemRequest extends FormRequest
{
    protected string $controller = 'ItineraryItemController';

    public function authorize(): bool { return true; }

    public function rules(): array { return []; }

    protected function failedValidation(Validator $validator)
    {
        /** @var ItineraryItem|null $itineraryItem */
        $itineraryItem = $this->route('itinerary_item');

        LoggerHelper::validationFailed($this->controller, 'toggle', $validator->errors()->toArray(), [
            'entity'    => 'itinerary_item',
            'entity_id' => $itineraryItem?->item_id,
            'user_id'   => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }
}
