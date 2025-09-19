<?php

namespace App\Http\Requests\Reviews;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewReplyRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'review_id' => ['required','exists:reviews,id'],
            'body'      => ['required','string','min:3','max:2000'],
            'public'    => ['nullable','boolean'],
        ];
    }
}
