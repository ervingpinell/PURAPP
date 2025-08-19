<?php

namespace App\Http\Requests\Tour\ItineraryItem;

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
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'is_active'   => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'El título es obligatorio.',
            'title.string'         => 'El título debe ser texto.',
            'title.max'            => 'El título no puede superar 255 caracteres.',
            'description.required' => 'La descripción es obligatoria.',
            'description.string'   => 'La descripción debe ser texto.',
            'description.max'      => 'La descripción no puede superar 2000 caracteres.',
            'is_active.boolean'    => 'El estado debe ser verdadero o falso.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        /** @var ItineraryItem|null $itineraryItem */
        $itineraryItem = $this->route('itinerary_item');

        LoggerHelper::validationFailed($this->controller, 'update', $validator->errors()->toArray(), [
            'entity'    => 'itinerary_item',
            'entity_id' => $itineraryItem?->item_id,
            'user_id'   => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }
}
