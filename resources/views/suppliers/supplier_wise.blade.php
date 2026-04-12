@extends('layout')
@section('title')
    Supplier purchases — {{ $supplier->name }}
@endsection
@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto fade-up">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <a href="{{ route('supplier.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                    <i class="fa-solid fa-arrow-left me-1"></i> Suppliers
                </a>
                <h4 class="fw-bold mb-0">Purchase invoices — {{ $supplier->name }}</h4>
                <p class="text-muted small mb-0">{{ $invoices->count() }} invoice(s)</p>
            </div>
        </div>

        <div class="card shadow-sm rounded-4 border-light">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Due</th>
                                <th class="text-end">Amount</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($invoices as $inv)
                                @php
                                    $badgeClass = match ($inv->status) {
                                        'paid' => 'bg-success-subtle text-success-emphasis',
                                        'pending' => 'bg-warning-subtle text-warning-emphasis',
                                        'overdue' => 'bg-danger-subtle text-danger-emphasis',
                                        default => 'bg-secondary-subtle text-secondary-emphasis',
                                    };
                                @endphp
                                <tr>
                                    <td class="fw-semibold">#{{ $inv->invoice_no }}</td>
                                    <td>{{ $inv->invoice_date->format('d-m-Y') }}</td>
                                    <td>{{ $inv->due_date->format('d-m-Y') }}</td>
                                    <td class="text-end">Rs. {{ number_format($inv->amount) }}</td>
                                    <td>
                                        <span class="badge rounded-pill {{ $badgeClass }}">{{ ucfirst($inv->status) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('purchase.show', $inv) }}" class="btn btn-sm btn-dark">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">No purchase invoices for this supplier.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
