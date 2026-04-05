<?php

namespace App\Notifications;

use App\Models\Sale;
use Illuminate\Notifications\Notification;

class SaleOverdueNotification extends Notification
{
    public function __construct(public Sale $sale) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'sale_id' => $this->sale->id,
            'invoice_no' => $this->sale->invoice_no,
            'amount' => $this->sale->amount,
            'due_date' => $this->sale->due_date->format('d M Y'),
            'type' => 'sale',
            'message' => 'Sale Invoice #'.$this->sale->invoice_no.' overdue ho gaya hai!',
        ];
    }
}
