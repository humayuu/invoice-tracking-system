@extends('layout')
@section('title')
    Reports
@endsection
@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto fade-up">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1">Reports</h4>
                <p class="text-muted small mb-0">Download PDF summaries of outstanding sales and purchases by party.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6" id="sales-summary">
                <div class="card shadow-sm rounded-4 border-0 h-100">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 48px; height: 48px;">
                                <i class="fa-solid fa-cart-shopping fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Sales summary</h5>
                                <p class="text-muted small mb-0">
                                    Every <strong>client</strong> with credit period, total <strong>pending</strong> (unpaid),
                                    <strong>overdue</strong>, and <strong>not yet due</strong> amounts.
                                </p>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-3 small">
                            <span class="badge rounded-pill text-bg-light border">
                                {{ $salesRows->count() }} clients
                            </span>
                            <span class="badge rounded-pill text-bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                Pending Rs. {{ number_format($salesPending, 0, '.', ',') }}
                            </span>
                            <span class="badge rounded-pill text-bg-danger-subtle text-danger-emphasis border border-danger-subtle">
                                Overdue Rs. {{ number_format($salesOverdue, 0, '.', ',') }}
                            </span>
                        </div>
                        <div class="mt-auto d-flex flex-wrap gap-2">
                            <a href="{{ route('reports.sales-summary.pdf') }}" class="btn btn-danger">
                                <i class="fa-solid fa-download me-2"></i>Download PDF
                            </a>
                            <a href="{{ route('reports.sales-summary.pdf') }}?preview=1" class="btn btn-outline-danger"
                                target="_blank" rel="noopener noreferrer" title="Open in new tab">
                                <i class="fa-solid fa-up-right-from-square me-2"></i>View PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6" id="purchase-summary">
                <div class="card shadow-sm rounded-4 border-0 h-100">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="rounded-3 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 48px; height: 48px;">
                                <i class="fa-solid fa-bag-shopping fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Purchase summary</h5>
                                <p class="text-muted small mb-0">
                                    Every <strong>supplier</strong> with credit period, total <strong>pending</strong>,
                                    <strong>overdue</strong>, and <strong>not yet due</strong> amounts.
                                </p>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-3 small">
                            <span class="badge rounded-pill text-bg-light border">
                                {{ $purchaseRows->count() }} suppliers
                            </span>
                            <span class="badge rounded-pill text-bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                Pending Rs. {{ number_format($purchasePending, 0, '.', ',') }}
                            </span>
                            <span class="badge rounded-pill text-bg-danger-subtle text-danger-emphasis border border-danger-subtle">
                                Overdue Rs. {{ number_format($purchaseOverdue, 0, '.', ',') }}
                            </span>
                        </div>
                        <div class="mt-auto d-flex flex-wrap gap-2">
                            <a href="{{ route('reports.purchase-summary.pdf') }}" class="btn btn-success">
                                <i class="fa-solid fa-download me-2"></i>Download PDF
                            </a>
                            <a href="{{ route('reports.purchase-summary.pdf') }}?preview=1" class="btn btn-outline-success"
                                target="_blank" rel="noopener noreferrer" title="Open in new tab">
                                <i class="fa-solid fa-up-right-from-square me-2"></i>View PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
