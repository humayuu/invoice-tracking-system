<?php

namespace App\Console\Commands;

use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\PurchaseOverdueNotification;
use App\Notifications\SaleOverdueNotification;
use Illuminate\Console\Command;

class CheckOverdueInvoices extends Command
{
    protected $signature = 'invoices:check-overdue';

    protected $description = 'Check overdue invoices and notify users';

    public function handle(): void
    {
        $this->checkSaleInvoices();
        $this->checkPurchaseInvoices();
        $this->info('Overdue check complete!');
    }

    private function checkSaleInvoices(): void
    {
        $overdueSales = Sale::query()
            ->where('status', 'pending')
            ->where('due_date', '<', now()->startOfDay())
            ->get();

        foreach ($overdueSales as $sale) {
            $user = User::find($sale->user_id);

            if ($user) {
                $alreadyNotified = $user->notifications()
                    ->where('type', SaleOverdueNotification::class)
                    ->whereJsonContains('data->sale_id', $sale->id)
                    ->exists();

                if (! $alreadyNotified) {
                    $user->notify(new SaleOverdueNotification($sale));
                    $this->info("Sale notified: #{$sale->invoice_no}");
                } else {
                    $this->info("Already notified: #{$sale->invoice_no}");
                }
            }
        }
    }

    private function checkPurchaseInvoices(): void
    {
        $overduePurchases = Purchase::query()
            ->where('status', 'pending')
            ->where('due_date', '<', now()->startOfDay())
            ->get();

        foreach ($overduePurchases as $purchase) {
            $user = User::find($purchase->user_id);

            if ($user) {
                $alreadyNotified = $user->notifications()
                    ->where('type', PurchaseOverdueNotification::class)
                    ->whereJsonContains('data->purchase_id', $purchase->id)
                    ->exists();

                if (! $alreadyNotified) {
                    $user->notify(new PurchaseOverdueNotification($purchase));
                    $this->info("Purchase notified: #{$purchase->invoice_no}");
                } else {
                    $this->info("Already notified: #{$purchase->invoice_no}");
                }
            }
        }
    }
}
