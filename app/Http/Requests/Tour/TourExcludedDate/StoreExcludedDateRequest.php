<?php

namespace App\Http\Requests\Tour\TourExcludedDate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;

class StoreExcludedDateRequest extends FormRequest
{
    protected string $controller = 'TourExcludedDateController';

    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tour_id'     => (int) $this->input('tour_id'),
            'schedule_id' => $this->filled('schedule_id') ? (int) $this->input('schedule_id') : null,
            'start_date'  => (string) $this->string('start_date')->trim(),
            'end_date'    => (string) $this->string('end_date')->trim(),
            'reason'      => (string) $this->string('reason')->trim()->squish(),
        ]);
    }

    public function rules(): array
    {
        return [
            'tour_id'     => ['required', 'exists:tours,tour_id'],
            'schedule_id' => ['nullable', 'exists:schedules,schedule_id'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['nullable', 'date', 'after_or_equal:start_date'],
            'reason'      => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'tour_id.required'    => 'El tour es obligatorio.',
            'tour_id.exists'      => 'El tour seleccionado no existe.',
            'schedule_id.exists'  => 'El horario seleccionado no existe.',
            'start_date.required' => 'La fecha inicial es obligatoria.',
            'start_date.date'     => 'La fecha inicial no es válida.',
            'end_date.date'       => 'La fecha final no es válida.',
            'end_date.after_or_equal' => 'La fecha final debe ser igual o posterior a la inicial.',
            'reason.string'       => 'El motivo debe ser texto.',
            'reason.max'          => 'El motivo no puede superar 255 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        LoggerHelper::validationFailed($this->controller, 'store', $validator->errors()->toArray(), [
            'entity'  => 'tour_excluded_date',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }
}
