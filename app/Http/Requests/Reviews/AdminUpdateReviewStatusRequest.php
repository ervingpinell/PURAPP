<?php

namespace App\Http\Requests\Reviews;

use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateReviewStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // el Policy del controlador valida admin
    }

    public function rules(): array
    {
        return [
            'status'    => ['required','in:pending,published,hidden,flagged'],
            'is_public' => ['nullable','boolean'],
            'is_verified' => ['nullable','boolean'],
        ];
    }
}
