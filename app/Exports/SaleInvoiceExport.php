<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SaleInvoiceExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $saleId;

    public function __construct($saleId)
    {
        $this->saleId = $saleId;
    }

    public function view(): View
    {
        $sale = Sale::with(['client', 'salesItems'])->findOrFail($this->saleId);

        return view('export.invoice', compact('sale'));
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Title row
            1 => [
                'font' => ['bold' => true, 'size' => 14],
            ],

            // Header row (items table)
            6 => [
                'font' => ['bold' => true],
            ],
        ];
    }
}
