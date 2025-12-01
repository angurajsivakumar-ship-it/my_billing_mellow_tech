<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
       "invoice_no", "customer_id", "total_amount", "total_tax", "amount_paid", "balance_returned"
    ];

    /**
     * @return HasMany
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function denominationTransactions()
    {
        return $this->hasMany(DenominationTransaction::class, 'invoice_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
