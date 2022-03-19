<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => ['required', Rule::unique('products')->ignore($this->id)],
            'name' => ['required', 'max:100', Rule::unique('products')->ignore($this->id)],
            'capacity' => 'required|integer',
            'photo' => 'image'
        ];
    }
}
