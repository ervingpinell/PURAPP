<?php

namespace App\Http\Requests\Reviews;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-reviews') ?? false;
    }

    protected function prepareForValidation(): void
    {
        // Nada especial, podrías normalizar fechas aquí si hace falta
    }

    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'integer', 'exists:bookings,booking_id'],
            'user_id'    => ['nullable', 'integer', 'exists:users,user_id'],
            'tour_id'    => ['required', 'integer', 'exists:tours,tour_id'],
            'email'      => ['required', 'email', 'max:150'],

            'expires_at' => ['nullable', 'date'],
        ];
    }
}
