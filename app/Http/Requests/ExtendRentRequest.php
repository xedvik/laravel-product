<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExtendRentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }
    public function rules(): array
    {
        return [
            'ownership_id' => 'required|exists:owner_ships,id',
            'additional_hours' => 'required|integer|in:4,8,12,24',
        ];
    }

    public function messages(): array
    {
        return [
            'ownership_id.required' => 'ID аренды обязателен',
            'ownership_id.exists' => 'Аренда не найдена',
            'additional_hours.required' => 'Время продления обязательно',
            'additional_hours.integer' => 'Время продления должно быть целым числом',
            'additional_hours.in' => 'Время продления должно быть 4, 8, 12 или 24 часа',
        ];
    }
}
