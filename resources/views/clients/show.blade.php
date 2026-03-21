@extends('layout')
@section('title')
    Client Detail
@endsection
@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Client Detail</h4>
            <a href="{{ route('client.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0">
                    <div class="card-body p-4">

                        <div class="mb-3">
                            <label class="form-label text-muted small">Name</label>
                            <p class="fw-semibold mb-0">{{ $client->name }}</p>
                        </div>
                        <hr>

                        <div class="mb-3">
                            <label class="form-label text-muted small">Email</label>
                            <p class="fw-semibold mb-0">{{ $client->email }}</p>
                        </div>
                        <hr>

                        <div class="mb-3">
                            <label class="form-label text-muted small">Phone</label>
                            <p class="fw-semibold mb-0">{{ $client->phone }}</p>
                        </div>
                        <hr>

                        <div class="mb-3">
                            <label class="form-label text-muted small">Credit Period</label>
                            <p class="fw-semibold mb-0">
                                <span class="badge fs-6 rounded-pill text-bg-primary">{{ $client->credit_period }}
                                    days</span>
                            </p>
                        </div>
                        <hr>

                        <div class="mb-3">
                            <label class="form-label text-muted small">Address</label>
                            <p class="fw-semibold mb-0">{{ $client->address }}</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
