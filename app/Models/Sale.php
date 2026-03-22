<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public static function generateInvoiceNo(): string
    {
        do {
            $invoice_no = 'INV-'.date('Ymd').'-'.rand(1000, 9999);
        } while (self::where('invoice_no', $invoice_no)->exists());

        return $invoice_no;
    }

    // ======= RelationShip ============//

    public function salesItems()
    {
        return $this->hasMany(SalesItem::class);
    }
}
