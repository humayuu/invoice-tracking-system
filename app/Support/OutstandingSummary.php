<?php

namespace App\Support;

use App\Models\Client;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Per-party unpaid totals for summary reports (pending = all unpaid; overdue = past due or status overdue).
 */
final class OutstandingSummary
{
    /**
     * @return Collection<int, object{name: string, credit_period: int, pending_total: float, overdue_total: float, not_yet_due: float}>
     */
    public static function forClients(int $userId): Collection
    {
        return Client::query()
            ->where('user_id', $userId)
            ->with([
                'sales' => static fn ($q) => $q->whereIn('status', ['pending', 'overdue']),
            ])
            ->orderBy('name')
            ->get()
            ->map(static function (Client $client): object {
                return self::aggregateParty(
                    $client->name,
                    (int) $client->credit_period,
                    $client->sales
                );
            });
    }

    /**
     * @return Collection<int, object{name: string, credit_period: int, pending_total: float, overdue_total: float, not_yet_due: float}>
     */
    public static function forSuppliers(int $userId): Collection
    {
        return Supplier::query()
            ->where('user_id', $userId)
            ->with([
                'purchases' => static fn ($q) => $q->whereIn('status', ['pending', 'overdue']),
            ])
            ->orderBy('name')
            ->get()
            ->map(static function (Supplier $supplier): object {
                return self::aggregateParty(
                    $supplier->name,
                    (int) $supplier->credit_period,
                    $supplier->purchases
                );
            });
    }

    /**
     * @param  Collection<int, Model>  $invoices
     */
    private static function aggregateParty(string $name, int $creditPeriod, Collection $invoices): object
    {
        $pending = 0.0;
        $overdue = 0.0;

        foreach ($invoices as $invoice) {
            $amt = (float) $invoice->amount;
            $pending += $amt;
            if (PendingInvoiceDue::isOverdueRow($invoice)) {
                $overdue += $amt;
            }
        }

        return (object) [
            'name' => $name,
            'credit_period' => $creditPeriod,
            'pending_total' => $pending,
            'overdue_total' => $overdue,
            'not_yet_due' => $pending - $overdue,
        ];
    }
}
