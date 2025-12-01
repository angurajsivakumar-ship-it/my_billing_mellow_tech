<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    protected $table = 'stock_logs';

    protected $fillable = [
        "product_id", "type", "quantity", "model_name", "model_id", "remark"
    ];
}
