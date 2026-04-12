@extends('layout')
@section('title')
    Suppliers
@endsection
@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Suppliers <span class="badge text-bg-dark fs-5">{{ $supplierCount }}</span></h4>
            <a href="{{ route('supplier.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-plus me-2"></i>New Supplier
            </a>
        </div>

        <div class="card shadow-sm rounded-4 border-light">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <input type="text" class="form-control" placeholder="Search clients..." style="max-width: 250px;">
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle w-100" id="supplierTable">
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
                            {{-- DataTables will populate this --}}
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
