<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'hours' => 'required|integer|in:4,8,12,24',
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
            'hours.in' => 'Количество часов должно быть 4, 8, 12 или 24',
            'unique_code.string' => 'Уникальный код должен быть строкой',
            'unique_code.max' => 'Уникальный код не должен превышать 255 символов',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Ошибка валидации данных',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
