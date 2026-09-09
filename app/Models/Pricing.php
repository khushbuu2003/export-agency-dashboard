<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_id',
        'base_export_price',
        'currency',
        'min_order_qty',
        'effective_date',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
