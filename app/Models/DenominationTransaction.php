<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DenominationTransaction extends Model
{
    protected $table = 'denomination_transactions';

    protected $fillable = [
        "invoice_id", "denomination_id", "count_used"
    ];

    /**
     * @return BelongsTo
     */
    public function denomination()
    {
        return $this->belongsTo(Denomination::class, 'denomination_id', 'id');
    }
}
