<?php

namespace App\Http\Requests\Product\Itinerary;

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
        // Check if this is a translation-only edit (from modal)
        if ($this->has('translations') && is_array($this->input('translations'))) {
            // Translation-only mode - no preparation needed
            return;
        }
        
        // Full itinerary edit mode - prepare fields
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

        // Translation-only edit (from modal with tabs)
        if ($this->has('translations') && is_array($this->input('translations'))) {
            return [
                'translations'                 => ['required', 'array'],
                'translations.*.name'          => ['nullable', 'string', 'max:255'],
                'translations.*.description'   => ['nullable', 'string', 'max:1000'],
                'translations.es.name'         => ['required', 'string', 'max:255'], // Spanish required
            ];
        }

        // Full itinerary edit (from main edit page)
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
            // Full edit mode messages
            'name.required'      => __('m_tours.itinerary.validation.name.required'),
            'name.string'        => __('m_tours.itinerary.validation.name.string'),
            'name.max'           => __('m_tours.itinerary.validation.name.max'),
            'name.unique'        => __('m_tours.itinerary.validation.name.unique'),
            'description.string' => __('m_tours.itinerary.validation.description.string'),
            'description.max'    => __('m_tours.itinerary.validation.description.max'),
            
            // Translation-only edit mode messages
            'translations.required'            => 'Las traducciones son requeridas.',
            'translations.array'               => 'Las traducciones deben ser un array.',
            'translations.*.name.string'       => 'El nombre debe ser texto.',
            'translations.*.name.max'          => 'El nombre no puede exceder 255 caracteres.',
            'translations.*.description.string'=> 'La descripción debe ser texto.',
            'translations.*.description.max'   => 'La descripción no puede exceder 1000 caracteres.',
            'translations.es.name.required'    => 'El nombre en español es obligatorio.',
            'translations.es.name.string'      => 'El nombre en español debe ser texto.',
            'translations.es.name.max'         => 'El nombre en español no puede exceder 255 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'        => __('m_tours.itinerary.fields.name'),
            'description' => __('m_tours.itinerary.fields.description'),
            'translations.es.name' => 'Nombre (Español)',
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
