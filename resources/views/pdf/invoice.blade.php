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

        /* Watermark */
        .watermark {
            position: fixed;
            top: 40%;
            left: 25%;
            transform: rotate(-30deg);
            font-size: 80px;
            color: rgba(0, 0, 0, 0.08);
            z-index: -1;
            font-weight: bold;
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
    {{-- Status Styling --}}
    @php
        $statusColor = match ($sale->status) {
            'paid' => '#d4edda',
            'pending' => '#fff3cd',
            'overdue' => '#f8d7da',
        };

        $textColor = match ($sale->status) {
            'paid' => '#155724',
            'pending' => '#856404',
            'overdue' => '#721c24',
        };

        $icon = match ($sale->status) {
            'paid' => '✔',
            'pending' => '⏳',
            'overdue' => '⚠',
        };
    @endphp

    {{-- Header --}}
    <div class="header">
        <table width="100%">
            <tr>
                <td>
                    <div class="title">INVOICE</div>
                    <div>#{{ $sale->invoice_no }}</div>
                </td>
                <td class="text-right">
                    <span class="badge" style="background: {{ $statusColor }}; color: {{ $textColor }};">
                        {{ $icon }} {{ strtoupper($sale->status) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    {{-- Client Info --}}
    <table class="info-table">
        <tr>
            <td><strong>Client:</strong> {{ $sale->client->name }}</td>
            <td><strong>Phone:</strong> {{ $sale->client->phone }}</td>
        </tr>
        <tr>
            <td><strong>Invoice Date:</strong> {{ $sale->invoice_date->format('d-m-Y') }}</td>
            <td><strong>Due Date:</strong> {{ $sale->due_date->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td><strong>PO No:</strong> {{ $sale->po_no ?? '-' }}</td>
            <td><strong>Credit Period:</strong> {{ $sale->client->credit_period }} days</td>
        </tr>
    </table>

    {{-- Items --}}
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
            @foreach ($sale->salesItems as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rs. {{ number_format($item->price) }}</td>
                    <td class="text-right">Rs. {{ number_format($item->total) }}</td>
                </tr>
            @endforeach

            <tr class="total-row">
                <td colspan="3" class="text-right">Total</td>
                <td class="text-right">Rs. {{ number_format($sale->amount) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Note --}}
    <div class="footer">
        <p><strong>Note:</strong></p>
        <p>{{ $sale->note ?? 'No note provided.' }}</p>
    </div>

</body>

</html>
