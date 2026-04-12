@extends('layout')

@section('title')
    Dashboard
@endsection

@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto fade-up dashboard-page">
        <header class="dashboard-header mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 pb-3 border-bottom border-secondary border-opacity-10">
                <div class="flex-grow-1" style="min-width: 200px;">
                    <h1 class="page-title mb-2">Dashboard</h1>
                    <p class="text-muted small mb-0 lh-base">Totals for your account and your latest invoices.</p>
                </div>
                <div class="dashboard-quick-actions d-flex flex-wrap gap-2 align-items-center">
                    <a href="{{ route('sales.create') }}" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-plus me-1"></i>New sale
                    </a>
                    <a href="{{ route('purchase.create') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-plus me-1"></i>New purchase
                    </a>
                    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-file-pdf me-1"></i>Reports
                    </a>
                </div>
            </div>
        </header>

        <section class="mb-4" aria-labelledby="dash-overview-heading">
            <h2 id="dash-overview-heading" class="dashboard-section-label">Overview</h2>
            <div class="row g-3 g-xl-4 stagger-children">
                <div class="col-sm-6 col-xl-3">
                    <div class="card kpi-card kpi-primary h-100 mb-0 border-0">
                        <div class="d-flex align-items-center p-4">
                            <div class="kpi-icon primary" aria-hidden="true"><i class="fa-solid fa-sack-dollar"></i></div>
                            <div class="kpi-info m-0 min-w-0 flex-grow-1">
                                <p class="text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.06em;">Total sales</p>
                                <h3 class="tabular-nums text-break">Rs. {{ number_format($totalSales, 0, '.', ',') }}</h3>
                                <span class="small text-muted">All invoices, any status</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card kpi-card kpi-warning h-100 mb-0 border-0">
                        <div class="d-flex align-items-center p-4">
                            <div class="kpi-icon warning" aria-hidden="true"><i class="fa-solid fa-bag-shopping"></i></div>
                            <div class="kpi-info m-0 min-w-0 flex-grow-1">
                                <p class="text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.06em;">Total purchases</p>
                                <h3 class="tabular-nums text-break">Rs. {{ number_format($totalPurchases, 0, '.', ',') }}</h3>
                                <span class="small text-muted">All bills, any status</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card kpi-card kpi-success h-100 mb-0 border-0">
                        <div class="d-flex align-items-center p-4">
                            <div class="kpi-icon success" aria-hidden="true"><i class="fa-solid fa-users"></i></div>
                            <div class="kpi-info m-0 min-w-0 flex-grow-1">
                                <p class="text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.06em;">Clients</p>
                                <h3 class="tabular-nums">{{ $activeClients }}</h3>
                                <span class="small text-muted">Saved in your account</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card kpi-card kpi-danger h-100 mb-0 border-0">
                        <div class="d-flex align-items-center p-4">
                            <div class="kpi-icon danger" aria-hidden="true"><i class="fa-solid fa-truck-field"></i></div>
                            <div class="kpi-info m-0 min-w-0 flex-grow-1">
                                <p class="text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.06em;">Suppliers</p>
                                <h3 class="tabular-nums">{{ $activeSuppliers }}</h3>
                                <span class="small text-muted">Saved in your account</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-2" aria-labelledby="dash-recent-heading">
            <div class="card dashboard-recent-card mb-0 border-0">
                <div class="card-header bg-transparent border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 px-4">
                    <div>
                        <h2 id="dash-recent-heading" class="h5 fw-bold mb-1">Recent invoices</h2>
                        <p class="small text-muted mb-0">Newest sales and purchases together.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary">
                            All sales
                        </a>
                        <a href="{{ route('purchase.index') }}" class="btn btn-sm btn-outline-secondary">
                            All purchases
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle dashboard-recent-table">
                            <thead class="table-thead">
                                <tr>
                                    <th class="ps-4" scope="col">Invoice</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Party</th>
                                    <th class="text-end" scope="col">Amount</th>
                                    <th class="pe-4" scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentInvoices as $row)
                                    @php
                                        $statusClass = match ($row['status']) {
                                            'paid' => 'bg-success-subtle text-success-emphasis',
                                            'pending' => 'bg-warning-subtle text-warning-emphasis',
                                            'overdue' => 'bg-danger-subtle text-danger-emphasis',
                                            default => 'bg-secondary-subtle text-secondary-emphasis',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="ps-4">
                                            <a href="{{ $row['url'] }}" class="dashboard-invoice-link d-inline-flex align-items-center gap-2">
                                                <span class="fw-semibold">{{ $row['invoice_no'] }}</span>
                                                <i class="fa-solid fa-chevron-right fa-xs opacity-50"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="text-muted small">{{ $row['date']->format('d M Y') }}</span>
                                        </td>
                                        <td>
                                            @if ($row['type'] === 'sale')
                                                <span class="badge rounded-pill bg-primary">Sale</span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary">Purchase</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-break">{{ $row['party'] }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-semibold tabular-nums">Rs. {{ number_format($row['amount'], 0, '.', ',') }}</span>
                                        </td>
                                        <td class="pe-4">
                                            <span class="badge rounded-pill {{ $statusClass }}">{{ ucfirst($row['status']) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-0 border-0">
                                            <div class="dashboard-empty-state text-center py-5 px-4">
                                                <div class="text-muted opacity-50 mb-3" aria-hidden="true">
                                                    <i class="fa-regular fa-file-lines fa-3x"></i>
                                                </div>
                                                <p class="text-muted mb-3 mb-md-4 mx-auto" style="max-width: 28rem;">
                                                    No invoices yet. Create a sale or a purchase to see them listed here.
                                                </p>
                                                <div class="d-flex flex-wrap justify-content-center gap-2">
                                                    <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">Create sale</a>
                                                    <a href="{{ route('purchase.create') }}" class="btn btn-outline-primary btn-sm">Create purchase</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
