@extends('layout')

@section('title')
    Users
@endsection

@php
    $moduleLabels = [
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
        <div
            class="d-flex flex-column flex-md-row flex-md-wrap align-items-stretch align-items-md-center justify-content-between gap-3 mb-3 mb-md-4">
            <h1 id="users-heading" class="h4 fw-bold mb-0">Users</h1>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary shadow-sm text-nowrap">
                <i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Add user
            </a>
        </div>

        {{-- Mobile: stacked cards --}}
        <section class="d-md-none" aria-labelledby="users-heading">
            @forelse ($users as $user)
                <div class="card shadow-sm border-0 rounded-3 mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div class="overflow-hidden flex-grow-1" style="min-width: 0;">
                                <div class="fw-semibold text-break">{{ $user->name }}</div>
                                <div class="small text-muted text-break">{{ $user->email }}</div>
                            </div>
                            <div class="d-flex flex-shrink-0 gap-1 align-items-start">
                                @if ($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-sm {{ $user->is_active ? 'btn-warning' : 'btn-success' }}"
                                            title="{{ $user->is_active ? 'Deactivate user' : 'Activate user' }}"
                                            aria-label="{{ $user->is_active ? 'Deactivate' : 'Activate' }} user {{ $user->name }}">
                                            @if ($user->is_active)
                                                <i class="fa-solid fa-user-slash" aria-hidden="true"></i>
                                            @else
                                                <i class="fa-solid fa-user-check" aria-hidden="true"></i>
                                            @endif
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary"
                                    title="Edit user" aria-label="Edit user {{ $user->name }}">
                                    <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                </a>
                                @if ($user->id !== auth()->id())
                                    <button type="button" class="btn btn-sm btn-danger btn-global-delete-confirm"
                                        data-bs-toggle="modal" data-bs-target="#globalDeleteModal"
                                        data-delete-url="{{ route('admin.users.destroy', $user) }}"
                                        data-delete-title="Delete user?"
                                        data-delete-message="{{ 'Permanently delete '.$user->name.' ('.$user->email.')?' }}"
                                        title="Delete user"
                                        aria-label="Delete user {{ $user->name }}">
                                        <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @if ($user->is_admin)
                                <span class="badge bg-primary">Administrator</span>
                            @else
                                <span class="badge bg-secondary">User</span>
                            @endif
                            @if ($user->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success">Active</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-25 text-secondary">Inactive</span>
                            @endif
                        </div>
                        <div class="small text-muted">
                            <span class="fw-semibold text-body-secondary">Access:</span>
                            @if ($user->is_admin)
                                All areas
                            @else
                                {{ collect($user->permissions ?? [])->map(fn ($k) => $moduleLabels[$k] ?? $k)->join(', ') ?: '—' }}
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body text-center text-muted py-4">No users yet.</div>
                </div>
            @endforelse
        </section>

        {{-- md+: table --}}
        <div class="card shadow-sm rounded-4 border-light d-none d-md-block">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" aria-labelledby="users-heading">
                        <caption class="visually-hidden">Users list with role, status, access, and actions</caption>
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="ps-3 ps-lg-4">Name</th>
                                <th scope="col" class="d-none d-lg-table-cell">Email</th>
                                <th scope="col" class="d-none d-xl-table-cell">Role</th>
                                <th scope="col" class="d-none d-xl-table-cell">Status</th>
                                <th scope="col" class="d-none d-lg-table-cell">Access</th>
                                <th scope="col" class="text-end pe-3 pe-lg-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td class="ps-3 ps-lg-4">
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                        <div class="small text-muted d-lg-none text-break">{{ $user->email }}</div>
                                        <div class="d-flex flex-wrap gap-1 mt-1 d-xl-none">
                                            @if ($user->is_admin)
                                                <span class="badge bg-primary">Admin</span>
                                            @else
                                                <span class="badge bg-secondary">User</span>
                                            @endif
                                            @if ($user->is_active)
                                                <span class="badge bg-success bg-opacity-10 text-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-25 text-secondary">Inactive</span>
                                            @endif
                                        </div>
                                        <div class="small text-muted mt-1 d-lg-none">
                                            @if ($user->is_admin)
                                                All areas
                                            @else
                                                {{ \Illuminate\Support\Str::limit(collect($user->permissions ?? [])->map(fn ($k) => $moduleLabels[$k] ?? $k)->join(', '), 40) ?: '—' }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="d-none d-lg-table-cell text-break">{{ $user->email }}</td>
                                    <td class="d-none d-xl-table-cell">
                                        @if ($user->is_admin)
                                            <span class="badge bg-primary">Administrator</span>
                                        @else
                                            <span class="badge bg-secondary">User</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-xl-table-cell">
                                        @if ($user->is_active)
                                            <span class="badge bg-success bg-opacity-10 text-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-25 text-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-lg-table-cell small text-muted">
                                        @if ($user->is_admin)
                                            All areas
                                        @else
                                            {{ collect($user->permissions ?? [])->map(fn ($k) => $moduleLabels[$k] ?? $k)->join(', ') ?: '—' }}
                                        @endif
                                    </td>
                                    <td class="text-end pe-3 pe-lg-4">
                                        <div class="d-inline-flex justify-content-end gap-1 flex-wrap">
                                            @if ($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm {{ $user->is_active ? 'btn-warning' : 'btn-success' }}"
                                                        title="{{ $user->is_active ? 'Deactivate user' : 'Activate user' }}"
                                                        aria-label="{{ $user->is_active ? 'Deactivate' : 'Activate' }} user {{ $user->name }}">
                                                        @if ($user->is_active)
                                                            <i class="fa-solid fa-user-slash" aria-hidden="true"></i>
                                                        @else
                                                            <i class="fa-solid fa-user-check" aria-hidden="true"></i>
                                                        @endif
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary"
                                                title="Edit user" aria-label="Edit user {{ $user->name }}">
                                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                            </a>
                                            @if ($user->id !== auth()->id())
                                                <button type="button"
                                                    class="btn btn-sm btn-danger btn-global-delete-confirm"
                                                    data-bs-toggle="modal" data-bs-target="#globalDeleteModal"
                                                    data-delete-url="{{ route('admin.users.destroy', $user) }}"
                                                    data-delete-title="Delete user?"
                                                    data-delete-message="{{ 'Permanently delete '.$user->name.' ('.$user->email.')?' }}"
                                                    title="Delete user"
                                                    aria-label="Delete user {{ $user->name }}">
                                                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No users yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
