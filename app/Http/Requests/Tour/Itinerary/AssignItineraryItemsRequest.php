<?php

namespace App\Http\Requests\Tour\Itinerary;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;

class AssignItineraryItemsRequest extends FormRequest
{
    protected string $controller = 'ItineraryController';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza el payload:
     * - Acepta item_ids[...] o items[...]
     * - Elimina claves "dummy" y valores vacíos
     * - Castea claves y valores a int
     */
    protected function prepareForValidation(): void
    {
        $raw = $this->input('items', $this->input('item_ids', []));

        // Si viene como colección de pares id => order
        if (is_array($raw)) {
            $clean = [];
            foreach ($raw as $key => $order) {
                if ($key === 'dummy') {
                    continue;
                }
                // descarta órdenes vacías / no numéricas
                if ($order === '' || $order === null) {
                    continue;
                }
                $id = (int) $key;
                $ord = (int) $order;
                if ($id > 0) {
                    $clean[$id] = $ord;
                }
            }
            $this->merge(['items' => $clean]);
        } else {
            $this->merge(['items' => []]);
        }
    }

    public function rules(): array
    {
        return [
            'items'   => ['required','array','min:1'],
            'items.*' => ['integer','min:0','max:9999'], // el orden
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Debes seleccionar al menos un ítem.',
            'items.array'    => 'El formato de los ítems no es válido.',
            'items.min'      => 'Debes seleccionar al menos un ítem.',
            'items.*.integer'=> 'El orden debe ser numérico.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        LoggerHelper::validationFailed($this->controller, 'assignItems', $validator->errors()->toArray(), [
            'entity'  => 'itinerary',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);

        parent::failedValidation($validator);
    }
}
