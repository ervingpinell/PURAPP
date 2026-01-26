<?php

namespace App\Http\Requests\Product\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class ToggleScheduleRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return []; }
}
