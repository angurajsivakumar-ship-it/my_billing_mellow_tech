<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Denomination extends Model
{
    protected $table = 'denominations';

    protected $fillable = [
        "value", "available_count"
    ];
}
