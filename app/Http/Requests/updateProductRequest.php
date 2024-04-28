<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'description' => 'nullable',
            'inventories.*.id' => 'required',
            'inventories.*.category' => 'required',
            'inventories.*.amount' => 'required|integer',
            'inventories.*.price' => 'required|integer',
            'files.*.image' => 'required|image',
            'files.*.thumbnail' => 'required|boolean'
        ];
    }
}
