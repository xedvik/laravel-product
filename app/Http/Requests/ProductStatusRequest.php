<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}