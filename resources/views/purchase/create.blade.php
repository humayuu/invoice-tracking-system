@extends('layout')
@section('title')
    Create Purchase Invoice
@endsection
@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto fade-up">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Create Purchase Invoice</h4>
            <a href="{{ route('purchase.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <form method="POST" action="{{ route('purchase.store') }}" id="addForm">
            @csrf
            <div class="row g-4">

                <div class="col-lg-4">
                    <div class="card shadow rounded-4 border-0 h-100">
                        <div class="card-body p-4">

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="supplierFilter">Supplier</label>
                                <select class="form-select" id="supplierFilter" name="supplier_id">
                                    <option value=""></option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" data-period="{{ $supplier->credit_period ?? 0 }}">
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="date" class="form-control" id="purchaseDate" name="invoice_date"
                                    placeholder="Date">
                                <label for="purchaseDate">Invoice Date</label>
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
                                                    class="form-control fw-bold" readonly></td>
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
                                <a href="{{ route('purchase.index') }}" class="btn btn-light px-4">Cancel</a>
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
        const supplierSelect = document.getElementById('supplierFilter');
        const purchaseDateInput = document.getElementById('purchaseDate');
        const dueDateInput = document.getElementById('dueDate');
        const form = document.getElementById('addForm');

        function showError(input, message) {
            input.classList.add('is-invalid');
            let feedback = input.parentElement.querySelector('.invalid-feedback');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                input.parentElement.appendChild(feedback);
            }
            feedback.textContent = message;
        }

        function clearError(input) {
            input.classList.remove('is-invalid');
            const feedback = input.parentElement.querySelector('.invalid-feedback');
            if (feedback) feedback.remove();
        }

        function validateSupplier() {
            if (!supplierSelect.value) {
                showError(supplierSelect, 'Please select a supplier');
                return false;
            }
            clearError(supplierSelect);
            return true;
        }

        function validateInvoiceDate() {
            if (!purchaseDateInput.value) {
                showError(purchaseDateInput, 'Please select invoice date');
                return false;
            }
            clearError(purchaseDateInput);
            return true;
        }

        function validatePoNumber() {
            const poInput = document.getElementById('poNumber');
            if (poInput.value && poInput.value.length > 50) {
                showError(poInput, 'Purchase Order No must be 50 characters or less');
                return false;
            }
            clearError(poInput);
            return true;
        }

        function validateItems() {
            const rows = document.querySelectorAll('#tbody tr');
            let hasValidItem = false;
            let hasErrors = false;

            rows.forEach((row) => {
                const itemName = row.querySelector('input[name*="item_name"]');
                const quantity = row.querySelector('input[name*="quantity"]');
                const price = row.querySelector('input[name*="price"]');

                if (!itemName.value.trim()) {
                    showError(itemName, 'Item name is required');
                    hasErrors = true;
                } else clearError(itemName);

                if (!quantity.value || parseFloat(quantity.value) < 1) {
                    showError(quantity, 'Quantity must be at least 1');
                    hasErrors = true;
                } else clearError(quantity);

                if (price.value === '' || parseFloat(price.value) < 0) {
                    showError(price, 'Price must be 0 or greater');
                    hasErrors = true;
                } else clearError(price);

                if (itemName.value.trim() && quantity.value && parseFloat(quantity.value) >= 1 && price.value !== '' &&
                    parseFloat(price.value) >= 0) {
                    hasValidItem = true;
                }
            });

            if (!hasValidItem && rows.length > 0) {
                const firstItemName = rows[0].querySelector('input[name*="item_name"]');
                if (firstItemName && !firstItemName.value.trim()) {
                    showError(firstItemName, 'At least one item is required');
                }
                return false;
            }

            if (rows.length === 0) return false;

            return !hasErrors && hasValidItem;
        }

        function validateForm() {
            return validateSupplier() && validateInvoiceDate() && validatePoNumber() && validateItems();
        }

        supplierSelect.addEventListener('change', validateSupplier);
        purchaseDateInput.addEventListener('change', validateInvoiceDate);
        document.getElementById('poNumber').addEventListener('input', validatePoNumber);

        document.getElementById('tbody').addEventListener('input', function(e) {
            if (e.target.classList.contains('qty') || e.target.classList.contains('price') || e.target.classList
                .contains('item_name')) {
                validateItems();
            }
        });

        window.addEventListener('itemsUpdated', function() {
            validateItems();
        });

        form.addEventListener('submit', function(e) {
            if (!validateForm()) e.preventDefault();
        });

        function calculateDueDate() {
            const invoiceDateValue = purchaseDateInput.value;
            if (!supplierSelect.value || !invoiceDateValue) {
                dueDateInput.value = '';
                return;
            }
            const selectedOption = supplierSelect.options[supplierSelect.selectedIndex];
            const creditDays = parseInt(selectedOption.dataset.period, 10);
            if (isNaN(creditDays)) {
                dueDateInput.value = '';
                return;
            }
            const [y, m, d] = invoiceDateValue.split('-').map(Number);
            const invoiceDate = new Date(y, m - 1, d);
            invoiceDate.setDate(invoiceDate.getDate() + creditDays);
            const year = invoiceDate.getFullYear();
            const month = String(invoiceDate.getMonth() + 1).padStart(2, '0');
            const day = String(invoiceDate.getDate()).padStart(2, '0');
            dueDateInput.value = `${year}-${month}-${day}`;
        }

        purchaseDateInput.addEventListener('change', calculateDueDate);
        let lastSupplierValue = '';
        setInterval(function() {
            if (supplierSelect.value !== lastSupplierValue) {
                lastSupplierValue = supplierSelect.value;
                calculateDueDate();
            }
        }, 200);
    </script>
@endsection
