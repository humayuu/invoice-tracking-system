<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'credit_period' => ['required', 'in:15,30,45,60'],
            'email' => [
                'nullable',
                'email',
                Rule::unique('clients', 'email')->where(fn ($q) => $q->where('user_id', Auth::id())),
            ],
            'phone' => ['nullable', 'string', 'min:11', 'max:15'],
            'address' => ['required', 'string', 'max:500'],
        ];
    }
}
