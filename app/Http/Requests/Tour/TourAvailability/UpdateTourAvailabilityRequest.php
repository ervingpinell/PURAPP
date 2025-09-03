<?php

namespace App\Http\Requests\Tour\TourAvailability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;
use App\Models\TourAvailability;

class UpdateTourAvailabilityRequest extends FormRequest
{
    protected string $controller = 'TourAvailabilityController';

    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tour_id'    => (int) $this->input('tour_id'),
            'date'       => (string) $this->string('date')->trim(),
            'start_time' => (string) $this->string('start_time')->trim(),
            'end_time'   => (string) $this->string('end_time')->trim(),
            'available'  => $this->has('available') ? $this->boolean('available') : null,
            'is_active'  => $this->has('is_active') ? $this->boolean('is_active') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'tour_id'    => ['bail','required','integer','exists:tours,tour_id'],
            'date'       => ['bail','required','date_format:Y-m-d'],
            'start_time' => ['nullable','date_format:H:i','required_with:end_time'],
            'end_time'   => ['nullable','date_format:H:i','after_or_equal:start_time'],
            'available'  => ['sometimes','boolean'],
            'is_active'  => ['sometimes','boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'tour_id.required'         => __('m_booking.availability.validation.tour_id.required'),
            'tour_id.integer'          => __('m_booking.availability.validation.tour_id.integer'),
            'tour_id.exists'           => __('m_booking.availability.validation.tour_id.exists'),
            'date.required'            => __('m_booking.availability.validation.date.required'),
            'date.date_format'         => __('m_booking.availability.validation.date.date_format'),
            'start_time.date_format'   => __('m_booking.availability.validation.start_time.date_format'),
            'start_time.required_with' => __('m_booking.availability.validation.start_time.required_with'),
            'end_time.date_format'     => __('m_booking.availability.validation.end_time.date_format'),
            'end_time.after_or_equal'  => __('m_booking.availability.validation.end_time.after_or_equal'),
            'available.boolean'        => __('m_booking.availability.validation.available.boolean'),
            'is_active.boolean'        => __('m_booking.availability.validation.is_active.boolean'),
        ];
    }

    public function attributes(): array
    {
        return [
            'tour_id'    => __('m_booking.availability.fields.tour'),
            'date'       => __('m_booking.availability.fields.date'),
            'start_time' => __('m_booking.availability.fields.start_time'),
            'end_time'   => __('m_booking.availability.fields.end_time'),
            'available'  => __('m_booking.availability.fields.available'),
            'is_active'  => __('m_booking.availability.fields.is_active'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        /** @var TourAvailability|null $availability */
        $availability = $this->route('availability');
        $action       = $this->route()?->getActionMethod() ?? 'update';

        LoggerHelper::validationFailed($this->controller, $action, $validator->errors()->toArray(), [
            'entity'    => 'tour_availability',
            'entity_id' => $availability?->getKey(),
            'user_id'   => optional($this->user())->getAuthIdentifier(),
        ]);
        parent::failedValidation($validator);
    }
}
