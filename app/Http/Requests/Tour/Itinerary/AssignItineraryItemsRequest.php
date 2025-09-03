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

    protected function prepareForValidation(): void
    {
        $raw = $this->input('items', $this->input('item_ids', []));

        if (is_array($raw)) {
            $clean = [];
            foreach ($raw as $key => $order) {
                if ($key === 'dummy') continue;
                if ($order === '' || $order === null) continue;

                $id  = (int) $key;
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
            'items'   => ['bail','required','array','min:1'],
            'items.*' => ['integer','min:0','max:9999'], // orden
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'    => __('m_tours.itinerary.validation.items.required'),
            'items.array'       => __('m_tours.itinerary.validation.items.array'),
            'items.min'         => __('m_tours.itinerary.validation.items.min'),
            'items.*.integer'   => __('m_tours.itinerary.validation.items.order_integer'),
            'items.*.min'       => __('m_tours.itinerary.validation.items.order_min'),
            'items.*.max'       => __('m_tours.itinerary.validation.items.order_max'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $action = $this->route()?->getActionMethod() ?? 'assignItems';

        LoggerHelper::validationFailed($this->controller, $action, $validator->errors()->toArray(), [
            'entity'  => 'itinerary',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);

        parent::failedValidation($validator);
    }
}
