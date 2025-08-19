<?php

namespace App\Http\Requests\Tour\Tour;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\LoggerHelper;

class StoreTourRequest extends FormRequest
{
    protected string $controller = 'TourController';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $normalizedNewSchedules = [];
        $incomingNewSchedules   = (array) $this->input('schedules_new', []);

        foreach ($incomingNewSchedules as $scheduleRow) {
            $startTimeNormalized = self::parseTimeTo24h($scheduleRow['start_time'] ?? null);
            $endTimeNormalized   = self::parseTimeTo24h($scheduleRow['end_time']   ?? null);

            $labelNormalized = isset($scheduleRow['label']) ? trim((string) $scheduleRow['label']) : null;
            $labelNormalized = $labelNormalized !== '' ? $labelNormalized : null;

            $capacityNormalized = isset($scheduleRow['max_capacity']) && $scheduleRow['max_capacity'] !== ''
                ? (int) $scheduleRow['max_capacity']
                : null;

            if ($startTimeNormalized && $endTimeNormalized) {
                $normalizedNewSchedules[] = [
                    'start' => $startTimeNormalized,
                    'end'   => $endTimeNormalized,
                    'label' => $labelNormalized,
                    'cap'   => $capacityNormalized,
                ];
            }
        }

        $this->merge([
            'schedules_existing' => $this->input('schedules_existing', []),
            'schedules_new_norm' => $normalizedNewSchedules,
        ]);
    }

    public function rules(): array
    {
        return [
            'name'                         => ['required','string','max:255'],
            'overview'                     => ['nullable','string'],
            'adult_price'                  => ['required','numeric','min:0'],
            'kid_price'                    => ['nullable','numeric','min:0'],
            'max_capacity'                 => ['required','integer','min:1'],
            'length'                       => ['required','numeric','min:1'],
            'tour_type_id'                 => ['required','exists:tour_types,tour_type_id'],

            'languages'                    => ['required','array','min:1'],
            'languages.*'                  => ['integer','exists:tour_languages,tour_language_id'],

            'amenities'                    => ['nullable','array'],
            'amenities.*'                  => ['integer','exists:amenities,amenity_id'],

            'excluded_amenities'           => ['nullable','array'],
            'excluded_amenities.*'         => ['integer','exists:amenities,amenity_id'],

            'itinerary_id'                 => ['required','exists:itineraries,itinerary_id'],

            'schedules_existing'           => ['array'],
            'schedules_existing.*'         => ['integer','exists:schedules,schedule_id'],

            'schedules_new_norm'               => ['array'],
            'schedules_new_norm.*.start'       => ['required','date_format:H:i'],
            'schedules_new_norm.*.end'         => ['required','date_format:H:i'],
            'schedules_new_norm.*.label'       => ['nullable','string','max:255'],
            'schedules_new_norm.*.cap'         => ['nullable','integer','min:1'],

            'viator_code'                  => ['nullable','string','max:255'],
            'color'                        => ['nullable','string','max:16'],
        ];
    }

    public function messages(): array
    {
        return [
            'languages.required' => 'Debes seleccionar al menos un idioma.',
            'languages.min'      => 'Debes seleccionar al menos un idioma.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validatorInstance) {
            $existingScheduleIds   = (array) $this->input('schedules_existing', []);
            $normalizedNewSchedules= (array) $this->input('schedules_new_norm', []);
            if (count($existingScheduleIds) === 0 && count($normalizedNewSchedules) === 0) {
                $validatorInstance->errors()->add('schedules', 'Debes seleccionar o crear al menos un horario.');
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        LoggerHelper::validationFailed($this->controller, 'store', $validator->errors()->toArray(), [
            'entity'  => 'tour',
            'user_id' => optional($this->user())->getAuthIdentifier(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Normaliza varios formatos comunes a 'H:i'.
     */
    private static function parseTimeTo24h(?string $rawTime): ?string
    {
        if (!$rawTime) {
            return null;
        }

        $timeLower  = trim(mb_strtolower($rawTime));
        $timeFormats = ['H:i', 'g:i a', 'g:iA', 'g:ia', 'g:i A', 'g:i'];

        foreach ($timeFormats as $supportedFormat) {
            $dateTime = \DateTime::createFromFormat($supportedFormat, $timeLower);
            if ($dateTime !== false) {
                return $dateTime->format('H:i');
            }
        }

        // Soporta "13:00 h"
        if (preg_match('/^\d{1,2}:\d{2}\s*h$/', $timeLower)) {
            $dateTime = \DateTime::createFromFormat('H:i \h', $timeLower);
            return $dateTime ? $dateTime->format('H:i') : null;
        }

        return null;
    }
}
