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
        // Check if using new translations array format
        if ($this->has('translations') && is_array($this->input('translations'))) {
            // New format - no preparation needed, validation will handle it
            return;
        }
        
        // Legacy format - prepare single fields
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
        // If using translations array (new tab-based modal)
        if ($this->has('translations') && is_array($this->input('translations'))) {
            return [
                'translations'              => ['required', 'array'],
                'translations.*.title'      => ['nullable', 'string', 'max:255'],
                'translations.*.description'=> ['nullable', 'string', 'max:1000'],
                'translations.es.title'     => ['required', 'string', 'max:255'], // Spanish required
                'is_active'                 => ['nullable', 'boolean'],
            ];
        }
        
        // Legacy single-field validation
        return [
            'title'       => ['bail','required','string','max:255'],
            'description' => ['bail','required','string','max:2000'],
            'is_active'   => ['nullable','boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            // Legacy format messages
            'title.required'       => __('m_tours.itinerary_item.validation.title.required'),
            'title.string'         => __('m_tours.itinerary_item.validation.title.string'),
            'title.max'            => __('m_tours.itinerary_item.validation.title.max'),
            'description.required' => __('m_tours.itinerary_item.validation.description.required'),
            'description.string'   => __('m_tours.itinerary_item.validation.description.string'),
            'description.max'      => __('m_tours.itinerary_item.validation.description.max'),
            
            // Translation array format messages
            'translations.required'         => 'Las traducciones son requeridas.',
            'translations.array'            => 'Las traducciones deben ser un array.',
            'translations.*.title.string'   => 'El título debe ser texto.',
            'translations.*.title.max'      => 'El título no puede exceder 255 caracteres.',
            'translations.*.description.string' => 'La descripción debe ser texto.',
            'translations.*.description.max'    => 'La descripción no puede exceder 1000 caracteres.',
            'translations.es.title.required'    => 'El título en español es obligatorio.',
            'translations.es.title.string'      => 'El título en español debe ser texto.',
            'translations.es.title.max'         => 'El título en español no puede exceder 255 caracteres.',
            
            'is_active.boolean'    => __('validation.boolean'),
        ];
    }

    public function attributes(): array
    {
        return [
            'title'       => __('m_tours.itinerary_item.fields.title'),
            'description' => __('m_tours.itinerary_item.fields.description'),
            'is_active'   => __('m_tours.itinerary_item.status.active'),
            'translations.es.title' => 'Título (Español)',
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
