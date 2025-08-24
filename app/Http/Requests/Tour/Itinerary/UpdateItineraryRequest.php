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
        // Normaliza antes de validar
        $this->merge([
            'name'        => $this->string('name')->trim()->toString(),
            'description' => $this->filled('description')
                ? $this->string('description')->trim()->toString()
                : null,
        ]);
    }

    public function rules(): array
    {
        // Asegúrate de que el parámetro de ruta se llame {itinerary}
        $itinerary = $this->route('itinerary'); // Model \App\Models\Itinerary
        $id        = $itinerary?->itinerary_id; // clave primaria personalizada

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('itineraries', 'name')->ignore($id, 'itinerary_id'),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => 'El nombre del itinerario es obligatorio.',
            'name.string'     => 'El nombre debe ser texto.',
            'name.max'        => 'El nombre no puede exceder 255 caracteres.',
            'name.unique'     => 'Ya existe un itinerario con ese nombre.',
            'description.string' => 'La descripción debe ser texto.',
            'description.max'    => 'La descripción no puede exceder 1000 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        LoggerHelper::validationFailed($this->controller, 'update', $validator->errors()->toArray(), [
            'entity'  => 'itinerary',
            'entity_id' => optional($this->route('itinerary'))->itinerary_id,
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);

        parent::failedValidation($validator);
    }
}
