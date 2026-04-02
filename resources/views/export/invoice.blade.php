<table>
    {{-- Title --}}
    <tr>
        <td colspan="4" style="font-size:16px; font-weight:bold;">
            Invoice #{{ $sale->invoice_no }}
        </td>
    </tr>

    <tr>
        <td colspan="4"><strong>Client:</strong> {{ $sale->client->name }}</td>
    </tr>

    <tr>
        <td><strong>Invoice Date:</strong> {{ $sale->invoice_date->format('d-m-Y') }}</td>
        <td><strong>Due Date:</strong> {{ $sale->due_date->format('d-m-Y') }}</td>
        <td><strong>PO No:</strong> {{ $sale->po_no ?? '-' }}</td>
        <td><strong>Status:</strong> {{ ucfirst($sale->status) }}</td>
    </tr>

    <tr>
        <td colspan="4"></td>
    </tr>

    {{-- Table Header --}}
    <tr style="background-color:#f2f2f2;">
        <th style="border:1px solid #000;">Item Name</th>
        <th style="border:1px solid #000;">Qty</th>
        <th style="border:1px solid #000;">Price</th>
        <th style="border:1px solid #000;">Subtotal</th>
    </tr>

    {{-- Items --}}
    @foreach ($sale->salesItems as $item)
        <tr>
            <td style="border:1px solid #000;">{{ $item->item_name }}</td>
            <td style="border:1px solid #000;">{{ $item->quantity }}</td>
            <td style="border:1px solid #000;">{{ $item->price }}</td>
            <td style="border:1px solid #000;">{{ $item->total }}</td>
        </tr>
    @endforeach

    {{-- Total --}}
    <tr>
        <td colspan="3" style="border:1px solid #000; font-weight:bold;">Total</td>
        <td style="border:1px solid #000; font-weight:bold;">
            {{ $sale->amount }}
        </td>
    </tr>

    <tr>
        <td colspan="4"></td>
    </tr>

    {{-- Note --}}
    <tr>
        <td colspan="4">
            <strong>Note:</strong> {{ $sale->note ?? 'No note' }}
        </td>
    </tr>
</table>
