<?php

namespace App\Http\Requests\Tour\TourType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTourTypeTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tourTypeId = $this->route('tourType')->tour_type_id;
        $locale = $this->route('locale');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // Validar unicidad del nombre en este locale, ignorando la traducción actual
                Rule::unique('tour_type_translations', 'name')
                    ->where('locale', $locale)
                    ->ignore(
                        $this->route('tourType')->translations()
                            ->where('locale', $locale)
                            ->value('id')
                    )
            ],
            'description' => 'nullable|string',
            'duration' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Ya existe un tipo de tour con este nombre en este idioma.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'duration.max' => 'La duración no puede exceder 255 caracteres.',
        ];
    }
}
