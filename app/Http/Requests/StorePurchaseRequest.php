<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'supplier_id' => [
                'required',
                Rule::exists('suppliers', 'id')->where(fn ($q) => $q->where('user_id', Auth::id())),
            ],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:invoice_date'],
            'po_no' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.sub_total' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Please select a supplier.',
            'supplier_id.exists' => 'The selected supplier is invalid.',
            'invoice_date.required' => 'Invoice date is required.',
            'due_date.required' => 'Due date is required.',
            'items.required' => 'Please add at least one item.',
            'items.*.item_name.required' => 'Each item must have a name.',
            'items.*.quantity.min' => 'Quantity must be greater than zero.',
            'items.*.price.min' => 'Price cannot be negative.',
        ];
    }
}
