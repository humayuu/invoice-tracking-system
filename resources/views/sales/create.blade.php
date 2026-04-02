@extends('layout')
@section('title')
    Create Invoice
@endsection
@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto fade-up">
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Create Sale Invoice</h4>
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <form method="POST" action="{{ route('sales.store') }}" id="addForm">
            @csrf
            <div class="row g-4">

                {{-- Left: Invoice Info --}}
                <div class="col-lg-4">
                    <div class="card shadow rounded-4 border-0 h-100">
                        <div class="card-body p-4">

                            <div class="form-floating mb-3">
                                <select class="form-select" id="clientFilter" name="client_id">
                                    <option value="" disabled selected>Select Client</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" data-period="{{ $client->credit_period }}">
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="date" class="form-control" id="saleDate" name="invoice_date"
                                    placeholder="Date">
                                <label for="saleDate">Invoice Date</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="date" class="form-control" id="dueDate" name="due_date"
                                    placeholder="Due Date" readonly>
                                <label for="dueDate">Due Date</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="poNumber" name="po_no"
                                    placeholder="Purchase Order No">
                                <label for="poNumber">Purchase Order No</label>
                            </div>

                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="note" name="note" placeholder="Note" style="height: 120px"></textarea>
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
                                        <tr>
                                            <td><input type="text" name="items[0][item_name]" class="form-control"></td>
                                            <td><input type="number" name="items[0][quantity]" class="form-control qty"
                                                    min="1"></td>
                                            <td><input type="number" name="items[0][price]" class="form-control price"
                                                    min="0" step="0.01"></td>
                                            <td><input type="text" name="items[0][sub_total]"
                                                    class="form-control sub-total" readonly></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger" disabled
                                                    onclick="removeItem(this)">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end fw-semibold">Total Amount</td>
                                            <td><input type="text" name="total_amount" id="total_amount"
                                                    class="form-control fw-bold" readonly>
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
                                <button type="submit" class="btn btn-primary px-4 shadow-sm">Create Invoice</button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        const clientSelect = document.getElementById('clientFilter');
        const saleDateInput = document.getElementById('saleDate');
        const dueDateInput = document.getElementById('dueDate');

        function calculateDueDate() {
            const selectedClient = clientSelect.options[clientSelect.selectedIndex];
            const invoiceDateValue = saleDateInput.value;

            // Only calculate if both a client is selected and a date is picked
            if (selectedClient && selectedClient.dataset.period && invoiceDateValue) {
                const creditDays = parseInt(selectedClient.dataset.period);
                const invoiceDate = new Date(invoiceDateValue);

                // Add the credit days to the invoice date
                invoiceDate.setDate(invoiceDate.getDate() + creditDays);

                // Format back to YYYY-MM-DD for the input field
                const year = invoiceDate.getFullYear();
                const month = String(invoiceDate.getMonth() + 1).padStart(2, '0');
                const day = String(invoiceDate.getDate()).padStart(2, '0');

                dueDateInput.value = `${year}-${month}-${day}`;
            }
        }

        // Event Listeners
        clientSelect.addEventListener('change', calculateDueDate);
        saleDateInput.addEventListener('change', calculateDueDate);
    </script>
@endsection
