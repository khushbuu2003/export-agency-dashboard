<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'total_qty',
        'reserved_qty',
        'unit',
        'unit_cost',
        'status',
    ];

    public function availableQty()
    {
        return max(0, $this->total_qty - $this->reserved_qty);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    public function pricings()
    {
        return $this->hasMany(Pricing::class);
    }

    public function exportTransactions()
    {
        return $this->hasMany(ExportTransaction::class);
    }
}
