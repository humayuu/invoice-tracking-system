@extends('layout')
@section('title')
    Invoice Detail
@endsection
@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Invoice Detail</h4>
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <div class="row g-4">

            {{-- Left: Invoice Info --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">

                        <h6 class="fw-bold text-muted mb-3 text-uppercase" style="font-size: 11px; letter-spacing: 1px;">
                            Client Info</h6>

                        <div class="mb-3">
                            <label class="form-label text-muted small">Client Name</label>
                            <p class="fw-semibold mb-0">{{ $sale->client->name }}</p>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Email</label>
                            <p class="fw-semibold mb-0">{{ $sale->client->email }}</p>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Phone</label>
                            <p class="fw-semibold mb-0">{{ $sale->client->phone }}</p>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Credit Period</label>
                            <p class="mb-0">
                                <span class="badge fs-6 rounded-pill text-bg-primary">{{ $sale->client->credit_period }}
                                    days</span>
                            </p>
                        </div>
                        <hr>
                        <div class="mb-0">
                            <label class="form-label text-muted small">Address</label>
                            <p class="fw-semibold mb-0">{{ $sale->client->address }}</p>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Right: Invoice Detail --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">

                        {{-- Invoice Header --}}
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h6 class="fw-bold text-muted mb-1 text-uppercase"
                                    style="font-size: 11px; letter-spacing: 1px;">Invoice</h6>
                                <h5 class="fw-bold mb-0">#{{ $sale->invoice_no }}</h5>
                            </div>
                            @php
                                $badgeClass = match ($sale->status) {
                                    'paid' => 'bg-success-subtle text-success-emphasis',
                                    'pending' => 'bg-warning-subtle text-warning-emphasis',
                                    'overdue' => 'bg-danger-subtle text-danger-emphasis',
                                };
                            @endphp
                            <span class="badge rounded-pill {{ $badgeClass }} fs-6">{{ ucfirst($sale->status) }}</span>
                        </div>

                        {{-- Invoice Meta --}}
                        <div class="row g-3 mb-4">
                            <div class="col-sm-4">
                                <p class="text-muted small mb-1">Invoice Date</p>
                                <p class="fw-semibold mb-0">{{ $sale->invoice_date->format('d-m-Y') }}</p>
                            </div>
                            <div class="col-sm-4">
                                <p class="text-muted small mb-1">Due Date</p>
                                <p class="fw-semibold mb-0">{{ $sale->due_date->format('d-m-Y') }}</p>
                            </div>
                            <div class="col-sm-4">
                                <p class="text-muted small mb-1">PO Number</p>
                                <p class="fw-semibold mb-0">{{ $sale->po_no ?? '—' }}</p>
                            </div>
                        </div>

                        {{-- Items Table --}}
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item Name</th>
                                        <th style="width: 120px" class="text-center">Quantity</th>
                                        <th style="width: 120px" class="text-center">Price</th>
                                        <th style="width: 130px" class="text-end">Sub Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sale->salesItems as $item)
                                        <tr>
                                            <td>{{ $item['item_name'] }}</td>
                                            <td class="text-center">{{ Number::format($item['quantity']) }}</td>
                                            <td class="text-center">Rs. {{ Number::format($item['price']) }}</td>
                                            <td class="text-end">Rs. {{ Number::format($item['total']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total Amount</td>
                                        <td class="text-end fw-bold">Rs. {{ Number::format($sale->amount) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Note --}}
                        <div class="mb-4">
                            <p class="text-muted small mb-1">Note</p>
                            <p class="mb-0">{{ $sale->note ?? 'No note added.' }}</p>
                        </div>
                        {{-- Note ke baad add karo --}}
                        <div class="d-flex justify-content-end gap-2">

                            @if ($sale->status !== 'paid')
                                <form action="" method="POST" class="d-inline">
                                    <button type="submit" class="btn btn-success px-4">
                                        <i class="fa-solid fa-check me-2"></i>Mark as Paid
                                    </button>
                                </form>
                            @endif

                            <a href="" class="btn btn-primary px-4">
                                <i class="fa-solid fa-pen me-2"></i>Edit
                            </a>

                            <form action="" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger px-4"
                                    onclick="return confirm('Delete this invoice?')">
                                    <i class="fa-solid fa-trash me-2"></i>Delete
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
