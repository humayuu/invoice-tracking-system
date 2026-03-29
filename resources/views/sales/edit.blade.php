@extends('layout')
@section('title')
    Edit Invoice
@endsection
@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto fade-up">
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Edit Sale Invoice</h4>
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <form method="POST" action="{{ route('sales.update', $sale->id) }}" id="addForm">
            @csrf
            @method('PUT')
            <div class="row g-4">

                {{-- Left: Invoice Info --}}
                <div class="col-lg-4">
                    <div class="card shadow rounded-4 border-0 h-100">
                        <div class="card-body p-4">

                            <div class="form-floating mb-3">
                                <select class="form-select" id="clientId" name="client_id">
                                    <option value="" disabled>Select Client</option>
                                    @forelse ($clients as $client)
                                        <option value="{{ $client->id }}"
                                            {{ $sale->client_id === $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
                                    @empty
                                        <option>No Record Found!</option>
                                    @endforelse
                                </select>
                                <label for="clientId">Client</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="date" class="form-control" id="saleDate" name="invoice_date"
                                    placeholder="Date" value="{{ $sale->invoice_date->format('Y-m-d') }}">
                                <label for="saleDate">Invoice Date</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="poNumber" name="po_no"
                                    placeholder="Purchase Order No" value="{{ $sale->po_no }}">
                                <label for="poNumber">Purchase Order No</label>
                            </div>

                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="note" name="note" placeholder="Note" style="height: 120px">{{ $sale->note }}</textarea>
                                <label for="note">Note</label>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Right: Sale Items --}}
                <div class="col-lg-8">
                    <div class="card shadow rounded-4 border-0">
                        <div class="card-body p-4">

                            <div class="table-responsive mb-2">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item Name</th>
                                            <th style="width: 140px">Quantity</th>
                                            <th style="width: 140px">Price</th>
                                            <th style="width: 140px">Sub Total</th>
                                            <th style="width: 50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody">
                                        @foreach ($sale->salesItems as $index => $item)
                                            <tr>
                                                <input type="hidden" name="items[{{ $index }}][id]"
                                                    value="{{ $item->id }}">
                                                <td><input type="text" name="items[{{ $index }}][item_name]"
                                                        class="form-control" value="{{ $item->item_name }}"></td>
                                                <td><input type="number" name="items[{{ $index }}][quantity]"
                                                        class="form-control qty" min="1"
                                                        value="{{ $item->quantity }}"></td>
                                                <td><input type="number" name="items[{{ $index }}][price]"
                                                        class="form-control price" min="0" step="0.01"
                                                        value="{{ $item->price }}"></td>
                                                <td><input type="text" name="items[{{ $index }}][sub_total]"
                                                        class="form-control sub-total" readonly
                                                        value="{{ $item->total }}"></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        {{ $loop->count === 1 ? 'disabled' : '' }}
                                                        onclick="removeItem(this)">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end fw-semibold">Total Amount</td>
                                            <td><input type="text" name="amount" id="total_amount"
                                                    class="form-control fw-bold" readonly value="{{ $sale->amount }}">
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="mb-3">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem()">
                                    <i class="fa-solid fa-plus me-1"></i> Add Item
                                </button>
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-3">
                                <a href="{{ route('sales.index') }}" class="btn btn-light px-4">Cancel</a>
                                <button type="reset" class="btn btn-outline-secondary px-4">
                                    <i class="fa-solid fa-rotate-left me-2"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary px-4 shadow-sm">Save Changes</button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
@endsection
