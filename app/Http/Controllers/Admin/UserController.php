<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()->orderBy('name')->get();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'moduleKeys' => User::MODULE_KEYS,
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $isAdmin = $request->boolean('is_admin');
        $permissions = $isAdmin ? null : ($validated['permissions'] ?? []);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_admin' => $isAdmin,
            'permissions' => $permissions,
            'is_active' => $request->boolean('is_active'),
            'email_verified_at' => now(),
        ]);

        return redirect()->back()->with([
            'success' => 'User Create Successfully',
            'flash_action' => 'created',
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'moduleKeys' => User::MODULE_KEYS,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();
        $isAdmin = $request->boolean('is_admin');
        $isActive = $request->boolean('is_active');
        $permissions = $isAdmin ? null : ($validated['permissions'] ?? []);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_admin' => $isAdmin,
            'is_active' => $isActive,
            'permissions' => $permissions,
        ];

        if ($request->filled('password')) {
            $data['password'] = $validated['password'];
        }

        $user->fill($data)->save();

        return redirect()->back()->with([
            'success' => 'User updated successfully.',
            'flash_action' => 'updated',
        ]);
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()->route('admin.users.index')->with([
                'error' => 'You cannot change your own account status here.',
                'flash_action' => 'delete_blocked',
            ]);
        }

        $willBeActive = ! $user->is_active;

        if (! $willBeActive && $user->is_admin) {
            $otherActiveAdmins = User::query()
                ->where('is_admin', true)
                ->where('is_active', true)
                ->where('id', '!=', $user->id)
                ->count();
            if ($otherActiveAdmins < 1) {
                return redirect()->route('admin.users.index')->with([
                    'error' => 'At least one active administrator is required.',
                    'flash_action' => 'delete_blocked',
                ]);
            }
        }

        $user->is_active = $willBeActive;
        $user->save();

        return redirect()->route('admin.users.index')->with([
            'success' => $willBeActive ? 'User activated successfully.' : 'User deactivated successfully.',
            'flash_action' => 'updated',
        ]);
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()->route('admin.users.index')->with([
                'error' => 'You cannot delete your own account.',
                'flash_action' => 'delete_blocked',
            ]);
        }

        if ($user->is_admin && User::query()->where('is_admin', true)->count() <= 1) {
            return redirect()->route('admin.users.index')->with([
                'error' => 'You cannot remove the last administrator.',
                'flash_action' => 'delete_blocked',
            ]);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with([
            'success' => 'User Deleted successfully.',
            'flash_action' => 'deleted',
        ]);
    }
}
