@extends('layout')
@section('title')
    Purchase Invoice Detail
@endsection
<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #printable-invoice,
        #printable-invoice * {
            visibility: visible;
        }

        #printable-invoice {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .btn,
        .no-print {
            display: none !important;
        }
    }
</style>
@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('purchase.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </a>
                <h4 class="fw-bold mb-0">Purchase Invoice Detail</h4>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('purchase.invoice.export', $purchase->id) }}" class="btn btn-sm btn-secondary">
                    <i class="fa-solid fa-file-arrow-down me-1"></i> Export XLSX
                </a>
                <a href="{{ route('purchase.invoice.pdf', $purchase->id) }}" class="btn btn-sm btn-danger">
                    <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
                </a>
                <button onclick="window.print()" class="btn btn-sm btn-dark">
                    <i class="fa-solid fa-print me-1"></i> Print
                </button>
            </div>
        </div>

        <div id="printable-invoice" class="row g-4">

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <p class="text-uppercase fw-bold text-muted mb-3" style="font-size:11px; letter-spacing:1px;">Supplier
                            info</p>

                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary
                                    d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                style="width:44px;height:44px;font-size:15px;">
                                {{ function_exists('mb_strtoupper') ? mb_strtoupper(mb_substr($purchase->supplier->name, 0, 2)) : strtoupper(substr($purchase->supplier->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="fw-semibold mb-0">{{ $purchase->supplier->name }}</p>
                                <p class="text-muted small mb-0">{{ $purchase->supplier->email }}</p>
                            </div>
                        </div>
                        <hr>

                        <div class="mb-3">
                            <p class="text-muted small mb-1">Phone</p>
                            <p class="fw-semibold mb-0">{{ $purchase->supplier->phone }}</p>
                        </div>
                        <hr>

                        <div class="mb-3">
                            <p class="text-muted small mb-1">Credit period</p>
                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary fs-6">
                                {{ $purchase->supplier->credit_period }} days
                            </span>
                        </div>
                        <hr>

                        <div>
                            <p class="text-muted small mb-1">Address</p>
                            <p class="text-muted mb-0" style="font-size:13px;">{{ $purchase->supplier->address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">

                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <p class="text-uppercase fw-bold text-muted mb-1"
                                    style="font-size:11px;letter-spacing:1px;">Invoice</p>
                                <h5 class="fw-bold mb-0">#{{ $purchase->invoice_no }}</h5>
                            </div>
                            @php
                                $badgeClass = match ($purchase->status) {
                                    'paid' => 'bg-success-subtle text-success-emphasis',
                                    'pending' => 'bg-warning-subtle text-warning-emphasis',
                                    'overdue' => 'bg-danger-subtle text-danger-emphasis',
                                };
                            @endphp
                            <span class="badge rounded-pill {{ $badgeClass }} fs-6">
                                {{ ucfirst($purchase->status) }}
                            </span>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-4">
                                <div class="bg-body-secondary rounded-3 p-3">
                                    <p class="text-muted small mb-1">Invoice date</p>
                                    <p class="fw-semibold mb-0">{{ $purchase->invoice_date->format('d-m-Y') }}</p>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="bg-body-secondary rounded-3 p-3">
                                    <p class="text-muted small mb-1">Due date</p>
                                    <p class="fw-semibold mb-0 {{ $purchase->status === 'overdue' ? 'text-danger' : '' }}">
                                        {{ $purchase->due_date->format('d-m-Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="bg-body-secondary rounded-3 p-3">
                                    <p class="text-muted small mb-1">PO number</p>
                                    <p class="fw-semibold mb-0">{{ $purchase->po_no ?? '—' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle mb-0" style="font-size:13px;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item name</th>
                                        <th class="text-center" style="width:90px;">Qty</th>
                                        <th class="text-center" style="width:120px;">Price</th>
                                        <th class="text-end" style="width:130px;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchase->purchaseItems as $item)
                                        <tr>
                                            <td>{{ $item->item_name }}</td>
                                            <td class="text-center">{{ Number::format($item->quantity) }}</td>
                                            <td class="text-center">Rs. {{ Number::format($item->price) }}</td>
                                            <td class="text-end">Rs. {{ Number::format($item->total) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total amount</td>
                                        <td class="text-end fw-bold">Rs. {{ Number::format($purchase->amount) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="bg-body-secondary rounded-3 p-3 mb-4">
                            <p class="text-muted small mb-1">Note</p>
                            <p class="mb-0" style="font-size:13px;">{{ $purchase->note ?? 'No note added.' }}</p>
                        </div>

                        <div class="d-flex justify-content-end gap-2 flex-wrap">
                            @if ($purchase->status !== 'paid')
                                <button type="button" class="btn btn-success px-4" data-bs-toggle="modal"
                                    data-bs-target="#confirmPaidModal" title="Mark invoice as paid">
                                    <i class="fa-solid fa-check me-2"></i>Mark as Paid
                                </button>

                                <a href="{{ route('purchase.edit', $purchase->id) }}" class="btn btn-primary px-4"
                                    title="Edit invoice details">
                                    <i class="fa-solid fa-pen me-2"></i>Edit
                                </a>
                            @else
                                <span class="badge bg-success bg-opacity-25 text-success py-2 px-3">
                                    <i class="fa-solid fa-check-circle me-1"></i>Paid
                                </span>
                            @endif

                            <button type="button" class="btn btn-danger px-4" data-bs-toggle="modal"
                                data-bs-target="#confirmDeleteModal" title="Delete invoice permanently">
                                <i class="fa-solid fa-trash me-2"></i>Delete
                            </button>
                        </div>

                        @if ($purchase->status !== 'paid')
                            <div class="modal fade" id="confirmPaidModal" tabindex="-1">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header border-bottom">
                                            <h5 class="modal-title">
                                                <i class="fa-solid fa-question-circle text-warning me-2"></i>Confirm
                                                Payment
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="mb-0">Mark this purchase invoice as <strong>paid</strong>?</p>
                                        </div>
                                        <div class="modal-footer border-top">
                                            <button type="button" class="btn btn-light"
                                                data-bs-dismiss="modal">Cancel</button>
                                            <form action="{{ route('purchase.status', $purchase->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fa-solid fa-check me-1"></i>Confirm
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-0 pb-0">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center px-4 pb-0">
                                        <div class="mb-3">
                                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10"
                                                style="width: 70px; height: 70px;">
                                                <i class="fa-solid fa-trash-can text-danger" style="font-size: 28px;"></i>
                                            </div>
                                        </div>
                                        <h5 class="fw-bold mb-2">Delete Invoice?</h5>
                                        <p class="text-muted mb-1">Are you sure you want to delete this purchase invoice?</p>
                                        <p class="text-danger small mb-0">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i>
                                            This action cannot be undone.
                                        </p>
                                    </div>
                                    <div class="modal-footer border-0 justify-content-center gap-2 pt-4">
                                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                                            Cancel
                                        </button>
                                        <form action="{{ route('purchase.destroy', $purchase->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger px-4">
                                                <i class="fa-solid fa-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
