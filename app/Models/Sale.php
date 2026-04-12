<?php

namespace App\Models;

use App\Notifications\SaleOverdueNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'invoice_date',
        'due_date',
        'po_no',
        'invoice_no',
        'note',
        'amount',
        'status',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // -----------------------------------------------
    // When a Sale is deleted, automatically delete
    // its related overdue notification as well
    // -----------------------------------------------
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($sale) {
            User::find($sale->user_id)
                ?->notifications()
                ->where('type', SaleOverdueNotification::class)
                ->whereJsonContains('data->sale_id', $sale->id)
                ->delete();
        });
    }

    // -----------------------------------------------
    // Auto-generate unique invoice number
    // Keep regenerating until a unique one is found
    // -----------------------------------------------
    public static function generateInvoiceNo(): string
    {
        do {
            $invoice_no = 'INV-'.date('Ymd').'-'.random_int(1000, 9999);
        } while (self::where('invoice_no', $invoice_no)->exists());

        return $invoice_no;
    }

    // -----------------------------------------------
    // Check if an overdue notification already exists
    // for this sale - used to prevent duplicates
    // -----------------------------------------------
    public function overdueNotification()
    {
        return $this->hasMany(DatabaseNotification::class,
            'data->sale_id', 'id')
            ->where('type', SaleOverdueNotification::class);
    }

    // =============== Relationships =============== //

    public function salesItems()
    {
        return $this->hasMany(SalesItem::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
