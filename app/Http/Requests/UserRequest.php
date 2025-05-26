<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}