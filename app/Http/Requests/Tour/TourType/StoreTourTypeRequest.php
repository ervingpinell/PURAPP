<?php

namespace App\Http\Requests\Tour\TourType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use App\Services\LoggerHelper;

class StoreTourTypeRequest extends FormRequest
{
    protected string $controller = 'TourTypeController';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $name        = (string) $this->string('name')->trim()->squish();
        $description = (string) $this->string('description')->trim();
        $duration    = (string) $this->string('duration')->trim()->squish();

        $this->merge([
            'name'        => $name,
            'description' => $description === '' ? null : $description,
            'duration'    => $duration === '' ? null : $duration,
        ]);
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255', Rule::unique('tour_types', 'name')],
            'description' => ['nullable', 'string', 'max:1000'],
            'duration'    => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => 'El nombre es obligatorio.',
            'name.string'     => 'El nombre debe ser texto.',
            'name.max'        => 'El nombre no puede superar 255 caracteres.',
            'name.unique'     => 'Ya existe un tipo de tour con ese nombre.',
            'description.max' => 'La descripción no puede superar 1000 caracteres.',
            'duration.max'    => 'La duración no puede superar 255 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        LoggerHelper::validationFailed($this->controller, 'store', $validator->errors()->toArray(), [
            'entity'  => 'tour_type',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);

        parent::failedValidation($validator);
    }
}
