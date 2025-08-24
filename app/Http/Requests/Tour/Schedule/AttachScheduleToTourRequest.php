<?php

namespace App\Http\Requests\Tour\Schedule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;

class AttachScheduleToTourRequest extends FormRequest
{
    protected string $controller = 'TourScheduleController';

    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'schedule_id' => ['required', 'exists:schedules,schedule_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'schedule_id.required' => 'Debes seleccionar un horario.',
            'schedule_id.exists'   => 'El horario seleccionado no existe.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        LoggerHelper::validationFailed($this->controller, 'attach', $validator->errors()->toArray(), [
            'entity'  => 'tour_schedule_pivot',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }
}
