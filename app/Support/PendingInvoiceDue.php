<?php

namespace App\Support;

use App\Models\Purchase;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class PendingInvoiceDue
{
    public static function today(): Carbon
    {
        return Carbon::today();
    }

    public static function dueDay(Model $invoice): Carbon
    {
        $d = $invoice->due_date;

        return $d instanceof Carbon ? $d->copy()->startOfDay() : Carbon::parse($d)->startOfDay();
    }

    /**
     * True when the invoice should be treated as overdue for highlighting and amounts
     * (stored status or calendar past due).
     */
    public static function isOverdueRow(Model $invoice): bool
    {
        if ($invoice->status === 'overdue') {
            return true;
        }

        return self::dueDay($invoice)->lt(self::today());
    }

    /**
     * Label for "Over Due Days" column (matches statement-style wording).
     */
    public static function overdueDaysLabel(Model $invoice): string
    {
        $today = self::today();
        $due = self::dueDay($invoice);

        if ($due->lt($today)) {
            $days = (int) $due->diffInDays($today);

            return $days === 1 ? '1 Day' : "{$days} Days";
        }

        if ($due->eq($today)) {
            return 'Due today';
        }

        if ($invoice->status === 'overdue') {
            return 'Overdue';
        }

        return 'Yet to due';
    }

    /**
     * Line items only (ordered by id). Newlines between rows — safe with e() + nl2br in Blade.
     */
    public static function particular(Model $invoice): string
    {
        $itemLines = self::itemParticularLines($invoice);

        if ($itemLines === []) {
            return '—';
        }

        return implode("\n", $itemLines);
    }

    /**
     * @return list<string>
     */
    private static function itemParticularLines(Model $invoice): array
    {
        $out = [];
        foreach (self::orderedLineItems($invoice) as $item) {
            $line = self::formatSingleItemLine($item);
            if ($line !== '') {
                $out[] = $line;
            }
        }

        return $out;
    }

    /**
     * @return Collection<int, Model>
     */
    private static function orderedLineItems(Model $invoice): Collection
    {
        if ($invoice instanceof Sale) {
            $items = $invoice->relationLoaded('salesItems')
                ? $invoice->salesItems
                : $invoice->salesItems()->orderBy('id')->get();

            return $items->sortBy('id')->values();
        }

        if ($invoice instanceof Purchase) {
            $items = $invoice->relationLoaded('purchaseItems')
                ? $invoice->purchaseItems
                : $invoice->purchaseItems()->orderBy('id')->get();

            return $items->sortBy('id')->values();
        }

        return collect();
    }

    private static function formatSingleItemLine(Model $item): string
    {
        $name = trim((string) ($item->item_name ?? ''));
        if ($name === '') {
            return '';
        }

        $qtyStr = self::formatQuantity($item->quantity ?? null);
        $line = ($qtyStr !== null && $qtyStr !== '') ? "{$name} × {$qtyStr}" : $name;

        if ($item->price !== null) {
            $line .= ' @ '.self::formatMoney((float) $item->price);
        }

        if ($item->total !== null) {
            $line .= ' = '.self::formatMoney((float) $item->total);
        }

        return $line;
    }

    private static function formatQuantity(mixed $quantity): ?string
    {
        if ($quantity === null || $quantity === '') {
            return null;
        }

        $n = (float) $quantity;
        if (abs($n - round($n)) < 0.000001) {
            return (string) (int) round($n);
        }

        $s = number_format($n, 2, '.', '');

        return rtrim(rtrim($s, '0'), '.') ?: '0';
    }

    private static function formatMoney(float $amount): string
    {
        if (abs($amount - round($amount)) < 0.000001) {
            return number_format($amount, 0, '.', '');
        }

        $s = number_format($amount, 2, '.', '');

        return rtrim(rtrim($s, '0'), '.') ?: '0';
    }
}
