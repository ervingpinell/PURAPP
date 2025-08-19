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
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
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
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        LoggerHelper::validationFailed($this->controller, 'store', $validator->errors()->toArray(), [
            'entity'  => 'itinerary_item',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }
}
