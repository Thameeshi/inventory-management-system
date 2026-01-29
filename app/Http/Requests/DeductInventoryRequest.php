<?php

namespace App\Http\Requests;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class DeductInventoryRequest extends FormRequest
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
            'deductions' => 'required|array|min:1',
            'deductions.*.item_id' => 'required|exists:items,id',
            'deductions.*.quantity' => 'required|numeric|min:0.01',
            'deductions.*.note' => 'nullable|string|max:500',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $deductions = $this->input('deductions', []);

            foreach ($deductions as $index => $deduction) {
                $item = Item::find($deduction['item_id']);
                
                if ($item && $item->quantity < $deduction['quantity']) {
                    $validator->errors()->add(
                        "deductions.{$index}.quantity",
                        "Insufficient stock for '{$item->name}'. Available: {$item->quantity}"
                    );
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'deductions.required' => 'At least one deduction is required.',
            'deductions.*.item_id.required' => 'Item selection is required.',
            'deductions.*.item_id.exists' => 'Selected item does not exist.',
            'deductions.*.quantity.required' => 'Quantity is required.',
            'deductions.*.quantity.min' => 'Quantity must be greater than 0.',
        ];
    }
}
