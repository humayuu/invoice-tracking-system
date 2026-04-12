<?php

namespace App\Exports;

use App\Support\PendingInvoiceDue;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PendingOverdueStatementExport implements FromCollection, WithColumnWidths, WithEvents, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private int $serial = 0;

    public function __construct(
        private readonly string $sheetTitle,
        private readonly Collection $invoices,
    ) {}

    public function collection(): Collection
    {
        $this->serial = 0;

        return $this->invoices;
    }

    public function title(): string
    {
        return Str::limit($this->sheetTitle, 31, '');
    }

    public function headings(): array
    {
        return [
            'Sl No',
            'Date',
            'Invoice No',
            'PO#',
            'Particular',
            'Amount',
            'Due Date',
            'Over Due Days',
        ];
    }

    public function map($invoice): array
    {
        $this->serial++;

        return [
            $this->serial,
            $invoice->invoice_date->format('d/m/Y'),
            $invoice->invoice_no,
            $invoice->po_no ?? '—',
            self::safeExcelCell(PendingInvoiceDue::particular($invoice)),
            number_format((float) $invoice->amount, 0, '.', ','),
            $invoice->due_date->format('d/m/Y'),
            PendingInvoiceDue::overdueDaysLabel($invoice),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 14,
            'C' => 18,
            'D' => 12,
            'E' => 52,
            'F' => 14,
            'G' => 14,
            'H' => 18,
        ];
    }

    /**
     * Avoid spreadsheet formula injection when invoice text starts with =, +, -, or @.
     */
    private static function safeExcelCell(string $value): string
    {
        if (preg_match('/^\s*[\x09=+\-@]/u', $value) === 1) {
            return "'".$value;
        }

        return $value;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                if ($highestRow < 2) {
                    return;
                }

                $sheet->getStyle("E2:E{$highestRow}")
                    ->getAlignment()
                    ->setWrapText(true)
                    ->setVertical(Alignment::VERTICAL_TOP);
            },
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8F9FA'],
                ],
            ],
        ];
    }
}
