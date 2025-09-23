<?php

namespace App\Http\Requests\Reviews;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocalReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-reviews') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_public'   => filter_var($this->input('is_public', true), FILTER_VALIDATE_BOOLEAN),
            'is_verified' => filter_var($this->input('is_verified', false), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules(): array
    {
        return [
            'tour_id'      => ['sometimes', 'integer', 'exists:tours,tour_id'],
            'booking_id'   => ['nullable', 'integer', 'exists:bookings,booking_id'],
            'user_id'      => ['nullable', 'integer', 'exists:users,user_id'],

            'rating'       => ['sometimes', 'integer', 'min:1', 'max:5'],
            'title'        => ['nullable', 'string', 'max:150'],
            'body'         => ['sometimes', 'string', 'max:5000'],

            'language'     => ['nullable', 'string', 'max:8'],
            'author_name'  => ['nullable', 'string', 'max:120'],
            'author_country' => ['nullable', 'string', 'max:120'],

            'is_verified'  => ['required', 'boolean'],
            'is_public'    => ['required', 'boolean'],
            'status'       => ['nullable', 'in:pending,published,hidden,flagged'],
        ];
    }
}
