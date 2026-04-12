<?php

namespace App\Exports;

use App\Models\Purchase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchaseInvoiceExport implements FromView, ShouldAutoSize, WithStyles
{
    public function __construct(protected int $purchaseId) {}

    public function view(): View
    {
        $purchase = Purchase::with(['supplier', 'purchaseItems'])
            ->where('user_id', Auth::id())
            ->findOrFail($this->purchaseId);

        return view('export.purchase_invoice', compact('purchase'));
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14],
            ],
            6 => [
                'font' => ['bold' => true],
            ],
        ];
    }
}
