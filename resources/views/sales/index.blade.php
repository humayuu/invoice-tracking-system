@extends('layout')
@section('title')
    Sales
@endsection
@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto fade-up">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Sales List <span class="badge text-bg-dark fs-5">{{ $salesCount }}</span></h4>
            <a href="{{ route('sales.create') }}" class="btn btn-primary shadow-sm"><i class="fa-solid fa-plus me-2"></i>Create
                Invoice</a>
        </div>

        <div class="card shadow-sm rounded-4 border-light">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search invoices..."
                        style="max-width: 250px;">
                </div>
                <div>
                    <button class="btn btn-outline-secondary btn-sm" id="exportBtn">
                        <i class="fa-solid fa-file-csv me-2"></i>Export CSV
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-thead">
                            <tr class="text-center">
                                <th class="ps-4">Invoice #</th>
                                <th>Date</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr class="text-center">
                                    <td class="ps-4 fw-semibold">#{{ $sale->invoice_no }}</td>
                                    <td class="text-muted">{{ $sale->invoice_date->format('d-m-Y') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold"
                                                style="width:34px; height:34px; font-size:13px;">AC</div>
                                            <span>{{ $sale->client->name }}</span>
                                        </div>
                                    </td>
                                    <td class="fw-semibold">Rs. {{ Number::format($sale->amount) }}</td>
                                    <td>
                                        <span
                                            class="badge rounded-pill bg-warning-subtle text-warning-emphasis fs-6">{{ ucfirst($sale->status) }}</span>
                                    </td>
                                    <td>

                                        <form action="" method="POST" class="d-inline">
                                            <button type="submit" class="btn btn-sm btn-success rounded-3 me-1"
                                                title="Mark as Paid">
                                                <i class="fa-solid fa-check me-1"></i>Mark Paid
                                            </button>
                                        </form>

                                        <a href="{{ route('sales.show', $sale->id) }}"
                                            class="btn btn-sm btn-dark border rounded-3 me-1" title="View">
                                            <i class="fa-solid fa-eye "></i>
                                        </a>
                                        <a href="{{ route('sales.edit', $sale->id) }}"
                                            class="btn btn-sm btn-primary border rounded-3 me-1" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <form action="#" method="POST" class="d-inline">
                                            <button type="submit" class="btn btn-sm btn-danger border rounded-3"
                                                title="Delete">
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
        </div>
    </div>
@endsection
