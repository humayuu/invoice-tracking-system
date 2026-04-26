@extends('layout')

@section('title')
    Add user
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
@endphp

@section('main')
    <div class="page-content p-2 p-md-4 flex-grow-1 overflow-auto fade-up">
        <h1 class="h4 fw-bold mb-3 mb-md-4">Add user</h1>

        <div class="card shadow-sm rounded-4 border-light">
            <div class="card-body p-3 p-md-4">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="user_name">Full name</label>
                            <input type="text" name="name" id="user_name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                autocomplete="name" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="user_email">Email</label>
                            <input type="email" name="email" id="user_email"
                                class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                autocomplete="email" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="user_password">Password</label>
                            <input type="password" name="password" id="user_password"
                                class="form-control @error('password') is-invalid @enderror" autocomplete="new-password"
                                required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="user_password_confirmation">Confirm password</label>
                            <input type="password" name="password_confirmation" id="user_password_confirmation"
                                class="form-control" autocomplete="new-password" required>
                        </div>

                        <div class="col-12">
                            <input type="hidden" name="is_active" value="0">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                    aria-describedby="user_active_help"
                                    @checked(old('is_active', '1') == '1')>
                                <label class="form-check-label fw-semibold" for="is_active">Account active</label>
                            </div>
                            <p class="text-muted small mb-0" id="user_active_help">Inactive users cannot sign in.</p>
                        </div>

                        <div class="col-12">
                            <input type="hidden" name="is_admin" value="0">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin" value="1"
                                    aria-describedby="user_admin_help"
                                    @checked(old('is_admin') == '1')>
                                <label class="form-check-label fw-semibold" for="is_admin">Administrator (full
                                    access)</label>
                            </div>
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
                                                @checked(in_array($key, old('permissions', []), true))>
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
                            <button type="submit" class="btn btn-primary flex-grow-1 flex-sm-grow-0">Create user</button>
                            <a href="{{ route('admin.users.index') }}"
                                class="btn btn-outline-secondary flex-grow-1 flex-sm-grow-0">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
