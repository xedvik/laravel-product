<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'unique_code' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'ID товара обязателен',
            'product_id.integer' => 'ID товара должен быть числом',
            'product_id.exists' => 'Товар не найден',
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
