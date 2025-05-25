<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\DTO\Products\ProductAuthorizationDTO;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('create', new ProductAuthorizationDTO());
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
}
