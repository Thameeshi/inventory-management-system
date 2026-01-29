<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request for updating an existing inventory item.
 * 
 * Validates item name uniqueness (excluding current item),
 * unit selection, and optional quantity/note fields.
 */
class UpdateItemRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('items')->ignore($this->route('item')->id),
            ],
            'unit' => 'required|string|in:kg,m,cm,pcs,ltr,box',
            'quantity' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Item name is required.',
            'name.unique' => 'An item with this name already exists.',
            'unit.required' => 'Unit is required.',
            'unit.in' => 'Invalid unit. Allowed: kg, m, cm, pcs, ltr, box.',
            'quantity.required' => 'Quantity is required.',
            'quantity.min' => 'Quantity cannot be negative.',
        ];
    }
}
