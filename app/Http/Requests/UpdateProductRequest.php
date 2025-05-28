<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\DTO\Products\ProductAuthorizationDTO;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('update', new ProductAuthorizationDTO());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           'name' => 'sometimes|string',
            'description' => 'nullable|string',
            'purchase_price' => 'sometimes|numeric|min:0',
            'rent_price_per_hour' => 'sometimes|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Имя должно быть строкой',
            'description.string' => 'Описание должно быть строкой',
            'purchase_price.numeric' => 'Цена покупки должна быть числом',
            'purchase_price.min' => 'Цена покупки должна быть не менее 0',
            'rent_price_per_hour.numeric' => 'Цена аренды за час должна быть числом',
            'rent_price_per_hour.min' => 'Цена аренды за час должна быть не менее 0',
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
