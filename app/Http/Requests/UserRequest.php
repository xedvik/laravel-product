<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }
    public function rules()
    {
        return [
            'amount' => 'sometimes|integer|min:0',
        ];
    }
    public function messages()
    {
        return [
            'amount.integer' => 'Сумма должна быть числом',
            'amount.min' => 'Сумма должна быть больше 0',
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
