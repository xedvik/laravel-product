<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }
    public function rules(): array
    {
        return [
            'product_id' => 'sometimes|exists:products,id',
            'unique_code' => 'sometimes|exists:owner_ships,unique_code',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.exists' => 'Товар не найден',
            'unique_code.exists' => 'Уникальный код не найден',
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
