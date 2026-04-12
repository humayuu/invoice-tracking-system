@extends('layout')
@section('title')
    {{ $supplier->name }} — Pending & overdue
@endsection
@php
    use App\Support\PendingInvoiceDue;
@endphp
@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto fade-up">
        <div class="container-fluid px-0">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <a href="{{ route('supplier.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                                <i class="fa-solid fa-arrow-left me-1"></i> Suppliers
                            </a>
                            <h4 class="mb-1">{{ $supplier->name }} — Pending &amp; overdue purchases</h4>
                            <p class="text-muted mb-0">
                                Credit Period: {{ $supplier->credit_period }} days
                                @if ($supplier->email)
                                    <span class="mx-2">|</span> {{ $supplier->email }}
                                @endif
                            </p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-primary print-button">
                                <i class="fa-solid fa-print me-1"></i> Print
                            </button>
                            <a href="{{ route('supplier.wise.invoices.pdf', $supplier->id) }}" class="btn btn-outline-danger">
                                <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
                            </a>
                            <a href="{{ route('supplier.wise.invoices.export', $supplier->id) }}" class="btn btn-outline-success">
                                <i class="fa-solid fa-file-excel me-1"></i> Export Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12" id="printArea">
                    <div class="print-header d-none">
                        <div class="text-center mb-4">
                            <h2 class="mb-1">Purchase statement</h2>
                            <h4 class="mb-3">{{ $supplier->name }}</h4>
                            <div class="mb-2">Credit Period: {{ $supplier->credit_period }} days</div>
                            <div class="mb-2">Statement date: {{ now()->format('d/m/Y') }}</div>
                            <div>Status: Pending &amp; overdue invoices only</div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover invoice-table mb-0" id="invoiceTable">
                                    <thead>
                                        <tr class="table-light">
                                            <th class="px-3">Sl No</th>
                                            <th>Date</th>
                                            <th>Invoice No</th>
                                            <th>PO#</th>
                                            <th>Particular</th>
                                            <th class="text-end">Amount</th>
                                            <th>Due date</th>
                                            <th>Over due days</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalAmount = 0;
                                            $overdueAmount = 0;
                                        @endphp
                                        @foreach ($invoices as $key => $invoice)
                                            @php
                                                $totalAmount += (float) $invoice->amount;
                                                $overdueRow = PendingInvoiceDue::isOverdueRow($invoice);
                                                if ($overdueRow) {
                                                    $overdueAmount += (float) $invoice->amount;
                                                }
                                            @endphp
                                            <tr class="invoice-row {{ $overdueRow ? 'table-danger' : '' }}">
                                                <td class="px-3">{{ $key + 1 }}</td>
                                                <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                                                <td class="fw-semibold">{{ $invoice->invoice_no }}</td>
                                                <td>{{ $invoice->po_no ?? '—' }}</td>
                                                @include('partials.invoice_particular_cell', ['invoice' => $invoice])
                                                <td class="text-end">{{ number_format($invoice->amount, 0, '.', ',') }}</td>
                                                <td>{{ $invoice->due_date->format('d/m/Y') }}</td>
                                                <td class="{{ $overdueRow ? 'text-danger fw-bold' : '' }}">
                                                    {{ PendingInvoiceDue::overdueDaysLabel($invoice) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    @if ($invoices->isNotEmpty())
                                        <tfoot>
                                            <tr class="border-top">
                                                <td colspan="5" class="text-end fw-bold">Total pending amount:</td>
                                                <td class="text-end fw-bold">{{ number_format($totalAmount, 0, '.', ',') }}</td>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-end fw-bold">Overdue amount:</td>
                                                <td class="text-end fw-bold text-danger">{{ number_format($overdueAmount, 0, '.', ',') }}</td>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-end fw-bold">Not yet due:</td>
                                                <td class="text-end fw-bold text-success">{{ number_format($totalAmount - $overdueAmount, 0, '.', ',') }}</td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </tfoot>
                                    @else
                                        <tfoot>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">No pending or overdue purchases for this supplier.</td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .invoice-table {
            font-size: 0.95rem;
        }

        .invoice-table .particular-cell {
            max-width: 24rem;
            min-width: 12rem;
            white-space: normal;
            word-break: break-word;
            font-size: 0.9rem;
            line-height: 1.35;
        }

        .invoice-table thead th {
            font-weight: 600;
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .invoice-row {
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
        }

        .invoice-row:hover {
            background-color: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }

        .invoice-table .table-danger {
            background-color: #fff3f3 !important;
        }

        .invoice-table .table-danger:hover {
            background-color: #ffe9e9 !important;
        }

        @media print {
            @page {
                size: landscape;
                margin: 15mm;
            }

            body * {
                visibility: hidden;
            }

            #printArea,
            #printArea * {
                visibility: visible;
            }

            #printArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .print-header.d-none {
                display: block !important;
            }

            .card {
                box-shadow: none !important;
                border: none !important;
            }

            .card-body {
                padding: 0 !important;
            }

            .invoice-row:hover {
                box-shadow: none !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            .table td,
            .table th {
                padding: 0.5rem !important;
                border: 1px solid #dee2e6 !important;
            }

            .table-danger {
                background-color: #fff3f3 !important;
                print-color-adjust: exact;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.print-button').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    window.print();
                });
            });
        });
    </script>
@endsection
