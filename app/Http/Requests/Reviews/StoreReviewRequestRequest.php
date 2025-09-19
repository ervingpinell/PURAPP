<?php

namespace App\Http\Requests\Reviews;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreReviewRequestRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'booking_id' => ['required','exists:bookings,id'],
            'user_id'    => ['nullable','exists:users,id'],
            'tour_id'    => ['required','exists:tours,id'],
            'email'      => ['required','email'],
            'expires_at' => ['nullable','date','after:now'],
        ];
    }
}
