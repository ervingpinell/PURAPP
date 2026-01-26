<?php

namespace App\Http\Requests\Reviews;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocalReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Usuarios invitados pueden dejar review local
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'provider'    => 'local',
            'is_public'   => true,
            'is_verified' => false,
            'status'      => 'pending',
            'language'    => $this->input('language', app()->getLocale()),
        ]);
    }

    public function rules(): array
    {
        return [
            'product_id'     => ['required', 'integer', 'exists:tours,product_id'],
            'booking_id'  => ['nullable', 'integer', 'exists:bookings,booking_id'],

            'provider'    => ['in:local'],
            'rating'      => ['required', 'integer', 'min:1', 'max:5'],
            'title'       => ['nullable', 'string', 'max:150'],
            'body'        => ['required', 'string', 'max:2000'],

            'language'    => ['nullable', 'string', 'max:8'],
            'author_name' => ['nullable', 'string', 'max:120'],

            'is_public'   => ['boolean'],
            'is_verified' => ['boolean'],
            'status'      => ['in:pending,published,hidden,flagged'],
        ];
    }

    public function validatedData(): array
    {
        // Útil para tu controlador (ya lo estás usando con method_exists)
        $data = $this->validated();
        unset($data['provider']); // provider se guarda en campo 'provider' del modelo? (en tu migración sí)
        $data['provider'] = 'local';

        return $data;
    }
}
