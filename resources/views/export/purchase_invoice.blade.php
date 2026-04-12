<table>
    <tr>
        <td colspan="4" style="font-size:16px; font-weight:bold;">
            Purchase #{{ $purchase->invoice_no }}
        </td>
    </tr>

    <tr>
        <td colspan="4"><strong>Supplier:</strong> {{ $purchase->supplier->name }}</td>
    </tr>

    <tr>
        <td><strong>Invoice Date:</strong> {{ $purchase->invoice_date->format('d-m-Y') }}</td>
        <td><strong>Due Date:</strong> {{ $purchase->due_date->format('d-m-Y') }}</td>
        <td><strong>PO No:</strong> {{ $purchase->po_no ?? '-' }}</td>
        <td><strong>Status:</strong> {{ ucfirst($purchase->status) }}</td>
    </tr>

    <tr>
        <td colspan="4"></td>
    </tr>

    <tr style="background-color:#f2f2f2;">
        <th style="border:1px solid #000;">Item Name</th>
        <th style="border:1px solid #000;">Qty</th>
        <th style="border:1px solid #000;">Price</th>
        <th style="border:1px solid #000;">Subtotal</th>
    </tr>

    @foreach ($purchase->purchaseItems as $item)
        <tr>
            <td style="border:1px solid #000;">{{ $item->item_name }}</td>
            <td style="border:1px solid #000;">{{ $item->quantity }}</td>
            <td style="border:1px solid #000;">{{ $item->price }}</td>
            <td style="border:1px solid #000;">{{ $item->total }}</td>
        </tr>
    @endforeach

    <tr>
        <td colspan="3" style="border:1px solid #000; font-weight:bold;">Total</td>
        <td style="border:1px solid #000; font-weight:bold;">
            {{ $purchase->amount }}
        </td>
    </tr>

    <tr>
        <td colspan="4"></td>
    </tr>

    <tr>
        <td colspan="4">
            <strong>Note:</strong> {{ $purchase->note ?? 'No note' }}
        </td>
    </tr>
</table>
