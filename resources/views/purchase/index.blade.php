@extends('layout')
@section('title')
    Purchases
@endsection
@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto fade-up">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Purchase List <span class="badge text-bg-dark fs-5">{{ $purchasesCount }}</span></h4>
            <a href="{{ route('purchase.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-plus me-2"></i>
                <span class="d-none d-sm-inline">Create Invoice</span>
            </a>
        </div>

        <div class="card shadow-sm rounded-4 border-light">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex gap-2">
                    <a href="{{ route('purchase.export') }}" class="btn btn-outline-dark btn-sm">
                        <i class="fa-solid fa-file-csv me-2"></i>
                        <span class="d-none d-sm-inline">Export CSV</span>
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle w-100" id="purchaseTable">
                        <thead class="table-thead">
                            <tr class="text-center">
                                <th>#</th>
                                <th>Invoice #</th>
                                <th class="d-none d-md-table-cell">Date</th>
                                <th>Supplier</th>
                                <th class="d-none d-md-table-cell">Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
