<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Sale::with(['client', 'salesItems'])->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Invoice No',
            'PO No',
            'Client',
            'Invoice Date',
            'Due Date',
            'Amount',
            'Status',
            'Note',
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->invoice_no,
            $sale->po_no ?? 'N/A',
            $sale->client->name ?? '-',
            $sale->invoice_date->format('d-m-Y'),
            $sale->due_date->format('d-m-Y'),
            number_format($sale->amount, 2),
            ucfirst($sale->status),
            $sale->note ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Bold header row
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
            'B' => 22,  // Invoice
            'C' => 15,  // PO No
            'D' => 22,  // Client
            'E' => 15,  // Invoice Date
            'F' => 15,  // Due Date
            'H' => 15,  // Amount
            'I' => 12,  // Status
            'J' => 25,  // Note
        ];
    }
}
