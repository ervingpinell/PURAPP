<?php

namespace App\Http\Requests\Reviews;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewProviderRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'name'         => ['required','string','max:120'],
            'slug'         => ['required','alpha_dash','max:50','unique:review_providers,slug,'.($this->route('provider')->id ?? 'NULL')],
            'driver'       => ['required','string','max:255'],
            'indexable'    => ['required','boolean'],
            'settings'     => ['nullable','array'],
            'cache_ttl_sec'=> ['required','integer','min:60','max:86400'],
            'is_active'    => ['required','boolean'],
        ];
    }
}
