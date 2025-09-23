<?php

namespace App\Http\Requests\Reviews;

use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateReviewStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-reviews') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_public' => filter_var($this->input('is_public', true), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules(): array
    {
        // Permite mandar 'status' directamente o una 'action' (publish/hide/flag)
        return [
            'status'    => ['nullable', 'in:pending,published,hidden,flagged'],
            'action'    => ['nullable', 'in:publish,hide,flag'],
            'is_public' => ['required', 'boolean'],
            'note'      => ['nullable', 'string', 'max:500'],
        ];
    }
}
