<?php

namespace App\Models;

use App\Notifications\PurchaseOverdueNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;

class Purchase extends Model
{
    protected $fillable = [
        'user_id',
        'supplier_id',
        'invoice_date',
        'due_date',
        'invoice_no',
        'po_no',
        'note',
        'amount',
        'status',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public static function generateInvoiceNo(): string
    {
        do {
            $invoice_no = 'INV-'.date('Ymd').'-'.random_int(1000, 9999);
        } while (self::where('invoice_no', $invoice_no)->exists());

        return $invoice_no;
    }

    public function overdueNotification()
    {
        return $this->hasMany(DatabaseNotification::class,
            'data->purchase_id', 'id')
            ->where('type', PurchaseOverdueNotification::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($purchase) {
            User::find($purchase->user_id)
                ?->notifications()
                ->where('type', PurchaseOverdueNotification::class)
                ->whereJsonContains('data->purchase_id', $purchase->id)
                ->delete();
        });
    }
}
