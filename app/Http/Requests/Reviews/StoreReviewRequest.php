<?php

namespace App\Http\Requests\Reviews;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'tour_id'   => ['required','exists:tours,id'],
            'rating'    => ['required','integer','min:1','max:5'],
            'title'     => ['nullable','string','max:120'],
            'body'      => ['required','string','min:10','max:3000'],
            'language'  => ['nullable','string','max:8'],
            'author'    => ['nullable','string','max:120'],
            'country'   => ['nullable','string','max:80'],
        ];
    }

   public function validatedData(): array
{
    $data = $this->validated(); // usa el original

    $data['provider'] = 'local';
    $data['is_public'] = true;
    $data['status'] = 'pending';
    $data['language'] = $data['language'] ?? app()->getLocale();
    $data['author_name'] = $data['author'] ?? null;
    $data['author_country'] = $data['country'] ?? null;

    unset($data['author'],$data['country']);
    return $data;
}
}
