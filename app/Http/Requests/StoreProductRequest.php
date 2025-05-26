<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\DTO\Products\ProductAuthorizationDTO;
class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->can('create', new ProductAuthorizationDTO());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'rent_price_per_hour' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Имя обязательно',
            'name.string' => 'Имя должно быть строкой',
            'description.string' => 'Описание должно быть строкой',
            'purchase_price.required' => 'Цена покупки обязательна',
            'purchase_price.numeric' => 'Цена покупки должна быть числом',
            'purchase_price.min' => 'Цена покупки должна быть не менее 0',
            'rent_price_per_hour.required' => 'Цена аренды за час обязательна',
            'rent_price_per_hour.numeric' => 'Цена аренды за час должна быть числом',
            'rent_price_per_hour.min' => 'Цена аренды за час должна быть не менее 0',
        ];
    }
}
