<?php

namespace App\Http\Requests\Reviews;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('reply-reviews') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'public' => filter_var($this->input('public', true), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules(): array
    {
        return [
            'body'   => ['required', 'string', 'max:3000'],
            'public' => ['required', 'boolean'],
        ];
    }
}
