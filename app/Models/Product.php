<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        "name", "product_code", "price", "tax_percentage", "available_stock"
    ];
}
