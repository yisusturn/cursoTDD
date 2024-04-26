<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'price' => 'required|min:1|numeric',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required',
            'price.required' => 'The price field is required',
            'price.min' => 'The price field must be at least 1',
            'price.numeric' => 'The price field must be a number',
            'category_id.required' => 'The category_id field is required',
            'category_id.exists' => 'The selected category_id is invalid',
            'stock.numeric' => 'The stock field must be a number',
        ];
    }
}
