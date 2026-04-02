@extends('layout')
@section('title')
    Sales
@endsection
@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto fade-up">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Sales List <span class="badge text-bg-dark fs-5">{{ $salesCount }}</span></h4>
            <a href="{{ route('sales.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-plus me-2"></i>
                <span class="d-none d-sm-inline">Create Invoice</span>
            </a>
        </div>

        <div class="card shadow-sm rounded-4 border-light">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex gap-2">

                    {{--  EXPORT BUTTON --}}
                    <a href="{{ route('sales.export') }}" class="btn btn-outline-dark btn-sm">
                        <i class="fa-solid fa-file-csv me-2"></i>
                        <span class="d-none d-sm-inline">Export CSV</span>
                    </a>

                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle w-100" id="salesTable">
                        <thead class="table-thead">
                            <tr class="text-center">
                                <th>#</th>
                                <th>Invoice #</th>
                                <th class="d-none d-md-table-cell">Date</th>
                                <th>Client</th>
                                <th class="d-none d-md-table-cell">Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- DataTables will populate this --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
