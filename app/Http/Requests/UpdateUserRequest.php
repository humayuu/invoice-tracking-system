<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'password' => ['nullable', 'string', Password::default(), 'confirmed'],
            'is_admin' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(User::MODULE_KEYS)],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var User $user */
            $user = $this->route('user');
            $isAdmin = $this->boolean('is_admin');
            $isActive = $this->boolean('is_active');
            $permissions = $isAdmin ? null : ($this->input('permissions') ?? []);

            if (! $isAdmin && (! is_array($permissions) || $permissions === [])) {
                $validator->errors()->add(
                    'permissions',
                    'Select at least one area, or mark the user as an administrator.'
                );
            }

            if ($user->is_admin && ! $isAdmin && User::query()->where('is_admin', true)->count() <= 1) {
                $validator->errors()->add('is_admin', 'There must be at least one administrator.');
            }

            if ($user->id === $this->user()->id && ! $isActive) {
                $validator->errors()->add('is_active', 'You cannot deactivate your own account.');
            }

            if ($user->is_admin && ! $isActive) {
                $otherActiveAdmins = User::query()
                    ->where('is_admin', true)
                    ->where('is_active', true)
                    ->where('id', '!=', $user->id)
                    ->count();
                if ($otherActiveAdmins < 1) {
                    $validator->errors()->add('is_active', 'At least one active administrator is required.');
                }
            }
        });
    }
}
