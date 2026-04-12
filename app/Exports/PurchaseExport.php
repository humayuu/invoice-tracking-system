<?php

namespace App\Exports;

use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchaseExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Purchase::with(['supplier', 'purchaseItems'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Invoice No',
            'PO No',
            'Supplier',
            'Invoice Date',
            'Due Date',
            'Amount',
            'Status',
            'Note',
        ];
    }

    public function map($purchase): array
    {
        return [
            $purchase->invoice_no,
            $purchase->po_no ?? 'N/A',
            $purchase->supplier->name ?? '-',
            $purchase->invoice_date->format('d-m-Y'),
            $purchase->due_date->format('d-m-Y'),
            number_format($purchase->amount, 2),
            ucfirst($purchase->status),
            $purchase->note ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF343A40'],
                ],
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 15,
            'C' => 22,
            'D' => 15,
            'E' => 15,
            'F' => 14,
            'G' => 12,
            'H' => 28,
        ];
    }
}
