<?php

namespace App\Notifications;

use App\Models\Purchase;
use Illuminate\Notifications\Notification;

class PurchaseOverdueNotification extends Notification
{
    public function __construct(public Purchase $purchase) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'purchase_id' => $this->purchase->id,
            'invoice_no' => $this->purchase->invoice_no,
            'amount' => $this->purchase->amount,
            'due_date' => $this->purchase->due_date->format('d M Y'),
            'type' => 'purchase',
            'message' => 'Purchase Invoice #'.$this->purchase->invoice_no.' overdue ho gaya hai!',
        ];
    }
}
