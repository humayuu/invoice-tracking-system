<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: A4 landscape;
            margin: 14mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
        }

        h2 {
            margin: 0 0 6px 0;
            font-size: 18px;
            text-align: center;
        }

        h4 {
            margin: 0 0 12px 0;
            font-size: 14px;
            text-align: center;
        }

        .meta {
            text-align: center;
            margin-bottom: 14px;
            line-height: 1.5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            vertical-align: top;
        }

        td.particular {
            font-size: 9px;
            line-height: 1.35;
            max-width: 200px;
            word-wrap: break-word;
        }

        thead th {
            background: #f1f3f5;
            font-weight: bold;
            text-align: left;
        }

        .text-end {
            text-align: right;
        }

        .text-danger {
            color: #b02a37;
            font-weight: bold;
        }

        /* Stronger red for overdue rows; target td so DomPDF paints cells reliably */
        tr.row-overdue td {
            background-color: #ffd4d4 !important;
            border-color: #f5b5b5 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        tr.row-overdue td.text-danger {
            color: #9a1428 !important;
        }

        tr.row-overdue td:first-child {
            border-left: 3px solid #c82333 !important;
        }

        tfoot td {
            font-weight: bold;
            background: #fafafa;
        }

        .t-overdue {
            color: #b02a37;
        }

        .t-due {
            color: #0f5132;
        }
    </style>
</head>

<body>
    @php
        use App\Support\PendingInvoiceDue;

        $totalAmount = 0;
        $overdueAmount = 0;
        foreach ($invoices as $inv) {
            $totalAmount += (float) $inv->amount;
            if (PendingInvoiceDue::isOverdueRow($inv)) {
                $overdueAmount += (float) $inv->amount;
            }
        }
        $notYetDueAmount = $totalAmount - $overdueAmount;
    @endphp

    <h2>{{ $documentTitle }}</h2>
    <h4>{{ $partyName }}</h4>
    <div class="meta">
        Credit Period: {{ $creditPeriodDays }} days<br>
        Statement date: {{ now()->format('d/m/Y') }}<br>
        {{ $statusLine }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:4%">Sl</th>
                <th style="width:9%">Date</th>
                <th style="width:12%">Invoice No</th>
                <th style="width:8%">PO#</th>
                <th>Particular</th>
                <th style="width:10%" class="text-end">Amount</th>
                <th style="width:9%">Due date</th>
                <th style="width:12%">Over due days</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoices as $key => $invoice)
                @php
                    $overdueRow = PendingInvoiceDue::isOverdueRow($invoice);
                @endphp
                <tr class="{{ $overdueRow ? 'row-overdue' : '' }}">
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                    <td>{{ $invoice->invoice_no }}</td>
                    <td>{{ $invoice->po_no ?? '—' }}</td>
                    <td class="particular">
                        @php $p = PendingInvoiceDue::particular($invoice); @endphp
                        @if ($p === '—')
                            —
                        @else
                            {!! nl2br(e($p)) !!}
                        @endif
                    </td>
                    <td class="text-end">{{ number_format($invoice->amount, 0, '.', ',') }}</td>
                    <td>{{ $invoice->due_date->format('d/m/Y') }}</td>
                    <td class="{{ $overdueRow ? 'text-danger' : '' }}">{{ PendingInvoiceDue::overdueDaysLabel($invoice) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:16px;">No pending or overdue invoices.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($invoices->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="5" class="text-end">Total pending amount:</td>
                    <td class="text-end">{{ number_format($totalAmount, 0, '.', ',') }}</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-end">Overdue amount:</td>
                    <td class="text-end t-overdue">{{ number_format($overdueAmount, 0, '.', ',') }}</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-end">Not yet due:</td>
                    <td class="text-end t-due">{{ number_format($notYetDueAmount, 0, '.', ',') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        @endif
    </table>
</body>

</html>
