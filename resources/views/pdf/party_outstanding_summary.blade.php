<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $documentTitle }} — InvoiceTracker</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 14mm;
        }

        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 10.5px;
            color: #1b1f24;
            margin: 0;
            line-height: 1.35;
        }

        .top-bar {
            background: #212529;
            color: #fff;
            padding: 10px 14px;
            margin-bottom: 12px;
            border-radius: 2px;
        }

        .top-bar table {
            width: 100%;
            border-collapse: collapse;
        }

        .top-bar td {
            padding: 0;
            vertical-align: middle;
        }

        .brand {
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #adb5bd;
        }

        .doc-title {
            font-size: 15px;
            font-weight: bold;
            text-align: right;
            color: #fff;
        }

        .meta {
            font-size: 10px;
            color: #495057;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }

        .totals-strip {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-bottom: 12px;
        }

        .totals-strip td {
            width: 33.33%;
            vertical-align: top;
            padding: 0;
        }

        .sum-card {
            border: 1px solid #dee2e6;
            border-radius: 3px;
            padding: 8px 10px;
            background: #f8f9fa;
        }

        .sum-card.danger {
            background: #fff5f5;
            border-color: #f1aeb5;
        }

        .sum-card.success {
            background: #f4fbf7;
            border-color: #a3cfbb;
        }

        .sum-label {
            font-size: 8px;
            text-transform: uppercase;
            font-weight: bold;
            color: #6c757d;
            letter-spacing: 0.05em;
        }

        .sum-val {
            font-size: 14px;
            font-weight: bold;
            margin-top: 4px;
        }

        .sum-card.danger .sum-val {
            color: #842029;
        }

        .sum-card.success .sum-val {
            color: #0f5132;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #ced4da;
            padding: 7px 8px;
            vertical-align: middle;
        }

        .data-table thead th {
            background: #e9ecef;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            font-weight: bold;
            color: #343a40;
            text-align: left;
        }

        .data-table thead th.text-end {
            text-align: right;
        }

        .data-table tbody tr:nth-child(even) {
            background: #fcfcfd;
        }

        tr.row-overdue td {
            background-color: #ffd4d4 !important;
            border-color: #f5b5b5 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        tr.row-overdue td:first-child {
            border-left: 3px solid #c82333 !important;
        }

        tr.row-overdue .amt-overdue {
            color: #9a1428 !important;
            font-weight: bold;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .data-table tfoot td {
            font-weight: bold;
            background: #f1f3f5;
            font-size: 10px;
        }

        .t-overdue {
            color: #842029;
        }

        .t-due {
            color: #0f5132;
        }

        .empty {
            text-align: center;
            padding: 24px;
            color: #6c757d;
        }

        .footer {
            margin-top: 14px;
            padding-top: 8px;
            border-top: 1px solid #dee2e6;
            font-size: 8.5px;
            color: #868e96;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="top-bar">
        <table>
            <tr>
                <td class="brand">InvoiceTracker</td>
                <td class="doc-title">{{ $documentTitle }}</td>
            </tr>
        </table>
    </div>

    <div class="meta">
        Unpaid invoices only (pending &amp; overdue status).
        &nbsp;·&nbsp; Statement date: <strong>{{ now()->format('d M Y') }}</strong>
    </div>

    <table class="totals-strip">
        <tr>
            <td>
                <div class="sum-card">
                    <div class="sum-label">Total pending</div>
                    <div class="sum-val">Rs. {{ number_format($totalPending, 0, '.', ',') }}</div>
                </div>
            </td>
            <td>
                <div class="sum-card danger">
                    <div class="sum-label">Total overdue</div>
                    <div class="sum-val">Rs. {{ number_format($totalOverdue, 0, '.', ',') }}</div>
                </div>
            </td>
            <td>
                <div class="sum-card success">
                    <div class="sum-label">Not yet due</div>
                    <div class="sum-val">Rs. {{ number_format($totalNotYetDue, 0, '.', ',') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width:4%" class="text-center">Sl</th>
                <th style="width:22%">{{ $partyColumnLabel }}</th>
                <th style="width:10%" class="text-center">Credit period (days)</th>
                <th style="width:16%" class="text-end">Pending amount (Rs.)</th>
                <th style="width:16%" class="text-end">Overdue amount (Rs.)</th>
                <th style="width:16%" class="text-end">Not yet due (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $i => $row)
                @php
                    $hasOverdue = $row->overdue_total > 0;
                @endphp
                <tr class="{{ $hasOverdue ? 'row-overdue' : '' }}">
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td><strong>{{ $row->name }}</strong></td>
                    <td class="text-center">{{ $row->credit_period }}</td>
                    <td class="text-end">Rs. {{ number_format($row->pending_total, 0, '.', ',') }}</td>
                    <td class="text-end amt-overdue">Rs. {{ number_format($row->overdue_total, 0, '.', ',') }}</td>
                    <td class="text-end">Rs. {{ number_format($row->not_yet_due, 0, '.', ',') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty">No {{ strtolower($partyColumnLabel) }}s found.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($rows->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end">Grand total</td>
                    <td class="text-end">Rs. {{ number_format($totalPending, 0, '.', ',') }}</td>
                    <td class="text-end t-overdue">Rs. {{ number_format($totalOverdue, 0, '.', ',') }}</td>
                    <td class="text-end t-due">Rs. {{ number_format($totalNotYetDue, 0, '.', ',') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">
        Generated {{ $generatedAt }} · Prepared by {{ $preparedBy }}
    </div>
</body>

</html>
