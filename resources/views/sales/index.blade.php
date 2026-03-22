@extends('layout')
@section('title')
    Sales
@endsection
@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto fade-up">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Sales List</h4>
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
                            <tr class="text-center">
                                <td class="ps-4 fw-semibold">#INV-0001</td>
                                <td class="text-muted">2025-03-01</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold"
                                            style="width:34px; height:34px; font-size:13px;">AC</div>
                                        <span>Acme Corporation</span>
                                    </div>
                                </td>
                                <td class="fw-semibold">$1,500.00</td>
                                <td>
                                    <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis">Pending</span>
                                </td>
                                <td>

                                    <form action="#" method="POST" class="d-inline">
                                        <input type="hidden" name="status" value="paid">
                                        <button type="submit" class="btn btn-sm btn-success rounded-3 me-1"
                                            title="Mark as Paid">
                                            <i class="fa-solid fa-check me-1"></i>Mark Paid
                                        </button>
                                    </form>

                                    <a href="#" class="btn btn-sm btn-dark border rounded-3 me-1" title="View">
                                        <i class="fa-solid fa-eye "></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-primary border rounded-3 me-1" title="Edit">
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
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                <span class="text-muted small" id="table-info">Showing 0 entries</span>
                <nav id="pagination-container"></nav>
            </div>
        </div>
    </div>
@endsection
