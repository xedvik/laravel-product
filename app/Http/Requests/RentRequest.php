<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'hours' => 'required|integer|min:1|max:24',
            'unique_code' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'ID товара обязателен',
            'product_id.integer' => 'ID товара должен быть числом',
            'product_id.exists' => 'Товар не найден',
            'hours.required' => 'Количество часов обязательно',
            'hours.integer' => 'Количество часов должно быть числом',
            'hours.min' => 'Количество часов должно быть не менее 1',
            'hours.max' => 'Количество часов должно быть не более 24',
            'unique_code.string' => 'Уникальный код должен быть строкой',
            'unique_code.max' => 'Уникальный код не должен превышать 255 символов',
        ];
    }
}
