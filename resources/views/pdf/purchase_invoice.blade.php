<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: A4;
            margin: 20px;
        }

        body {
            font-family: DejaVu Sans;
            font-size: 12px;
            color: #333;
            position: relative;
        }

        .header {
            margin-bottom: 20px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 4px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table th {
            background: #f5f5f5;
            border: 1px solid #ccc;
            padding: 8px;
        }

        .table td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        .text-right {
            text-align: right;
        }

        .total-row td {
            font-weight: bold;
            background: #fafafa;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }

        .footer {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    @php
        $statusColor = match ($purchase->status) {
            'paid' => '#d4edda',
            'pending' => '#fff3cd',
            'overdue' => '#f8d7da',
        };

        $textColor = match ($purchase->status) {
            'paid' => '#155724',
            'pending' => '#856404',
            'overdue' => '#721c24',
        };

        $icon = match ($purchase->status) {
            'paid' => '✔',
            'pending' => '⏳',
            'overdue' => '⚠',
        };
    @endphp

    <div class="header">
        <table width="100%">
            <tr>
                <td>
                    <div class="title">PURCHASE INVOICE</div>
                    <div>#{{ $purchase->invoice_no }}</div>
                </td>
                <td class="text-right">
                    <span class="badge" style="background: {{ $statusColor }}; color: {{ $textColor }};">
                        {{ $icon }} {{ strtoupper($purchase->status) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Supplier:</strong> {{ $purchase->supplier->name }}</td>
            <td><strong>Phone:</strong> {{ $purchase->supplier->phone }}</td>
        </tr>
        <tr>
            <td><strong>Invoice Date:</strong> {{ $purchase->invoice_date->format('d-m-Y') }}</td>
            <td><strong>Due Date:</strong> {{ $purchase->due_date->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td><strong>PO No:</strong> {{ $purchase->po_no ?? '-' }}</td>
            <td><strong>Credit Period:</strong> {{ $purchase->supplier->credit_period }} days</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th width="40%">Item</th>
                <th width="15%">Qty</th>
                <th width="20%">Price</th>
                <th width="25%" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchase->purchaseItems as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rs. {{ number_format($item->price) }}</td>
                    <td class="text-right">Rs. {{ number_format($item->total) }}</td>
                </tr>
            @endforeach

            <tr class="total-row">
                <td colspan="3" class="text-right">Total</td>
                <td class="text-right">Rs. {{ number_format($purchase->amount) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Note:</strong></p>
        <p>{{ $purchase->note ?? 'No note provided.' }}</p>
    </div>

</body>

</html>
