@extends('layout')

@section('title')
    Edit user
@endsection

@php
    $labels = [
        'dashboard' => 'Dashboard',
        'sales' => 'Sales',
        'purchase' => 'Purchase',
        'clients' => 'Clients',
        'suppliers' => 'Suppliers',
        'reports' => 'Reports',
    ];
    $selected = old('permissions', $user->permissions ?? []);
@endphp

@section('main')
    <div class="page-content p-2 p-md-4 flex-grow-1 overflow-auto fade-up">
        <h1 class="h4 fw-bold mb-3 mb-md-4">Edit user</h1>

        <div class="card shadow-sm rounded-4 border-light">
            <div class="card-body p-3 p-md-4">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="user_name">Full name</label>
                            <input type="text" name="name" id="user_name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" autocomplete="name" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="user_email">Email</label>
                            <input type="email" name="email" id="user_email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}" autocomplete="email" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="user_password">New password</label>
                            <input type="password" name="password" id="user_password"
                                class="form-control @error('password') is-invalid @enderror"
                                autocomplete="new-password" placeholder="Leave blank to keep current"
                                aria-describedby="user_password_hint">
                            <p class="form-text small mb-0" id="user_password_hint">Leave blank to keep the current password.</p>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="user_password_confirmation">Confirm new password</label>
                            <input type="password" name="password_confirmation" id="user_password_confirmation"
                                class="form-control" autocomplete="new-password" aria-describedby="user_password_hint">
                        </div>

                        <div class="col-12">
                            <input type="hidden" name="is_active" value="0">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                    aria-describedby="user_active_help"
                                    @checked(old('is_active', $user->is_active ? '1' : '0') == '1')>
                                <label class="form-check-label fw-semibold" for="is_active">Account active</label>
                            </div>
                            @error('is_active')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            <p class="text-muted small mb-0" id="user_active_help">Inactive users cannot sign in.</p>
                        </div>

                        <div class="col-12">
                            <input type="hidden" name="is_admin" value="0">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin" value="1"
                                    aria-describedby="user_admin_help"
                                    @checked(old('is_admin', $user->is_admin ? '1' : '0') == '1')>
                                <label class="form-check-label fw-semibold" for="is_admin">Administrator (full
                                    access)</label>
                            </div>
                            @error('is_admin')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            <p class="text-muted small mb-0" id="user_admin_help">Administrators can manage users and see every module.</p>
                        </div>

                        <fieldset class="col-12 border-0 p-0 m-0 min-w-0" id="permissions-block"
                            aria-describedby="permissions_hint">
                            <legend class="form-label fw-semibold float-none w-100 px-0">Module access</legend>
                            <p class="text-muted small" id="permissions_hint">Ignored if “Administrator” is checked.</p>
                            <div class="row g-2">
                                @foreach ($moduleKeys as $key)
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                                value="{{ $key }}" id="perm_{{ $key }}"
                                                @checked(in_array($key, $selected, true))>
                                            <label class="form-check-label" for="perm_{{ $key }}">{{ $labels[$key] ?? $key }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('permissions')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </fieldset>

                        <div class="col-12 mt-3 d-flex flex-column flex-sm-row gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1 flex-sm-grow-0">Save changes</button>
                            <a href="{{ route('admin.users.index') }}"
                                class="btn btn-outline-secondary flex-grow-1 flex-sm-grow-0">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
