@extends('layout')
@section('title')
    Clients
@endsection
@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">All Clients</h4>
            <a href="{{ route('client.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-plus me-2"></i>New Client
            </a>
        </div>

        <div class="card shadow-sm rounded-4 border-light">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <input type="text" class="form-control" placeholder="Search clients..." style="max-width: 250px;">
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-thead">
                            <tr class="text-center">
                                <th class="ps-4">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Credit Period</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $client)
                                <tr class="text-center">
                                    <td class="ps-4">{{ $loop->iteration }}</td>
                                    <td>{{ $client->name }}</td>
                                    <td>{{ $client->email }}</td>
                                    <td>{{ $client->phone }}</td>
                                    <td>
                                        <span class="badge rounded-pill text-bg-primary">{{ $client->credit_period }}
                                            days</span>
                                    </td>
                                    <td class="d-flex">
                                        <a href="#" class="btn btn-sm btn-secondary me-1">
                                            <i class="fa-solid fa-file-invoice"></i>
                                        </a>
                                        <a href="{{ route('client.show', $client->id) }}" class="btn btn-sm btn-dark me-1">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('client.edit', $client->id) }}"
                                            class="btn btn-sm btn-primary me-1">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <form method="POST" action="{{ route('client.destroy', $client->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                <span class="text-muted small">Showing 1 entry</span>
            </div>
        </div>
    </div>
@endsection
