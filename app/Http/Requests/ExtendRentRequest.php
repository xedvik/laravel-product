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
            'additional_hours' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'ownership_id.required' => 'ID аренды обязателен',
            'ownership_id.exists' => 'Аренда не найдена',
            'additional_hours.required' => 'Время продления обязательно',
            'additional_hours.integer' => 'Время продления должно быть целым числом',
            'additional_hours.min' => 'Время продления должно быть не менее 1 часа',
        ];
    }
}