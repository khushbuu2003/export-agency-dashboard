<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'customer_id',
        'stock_id',
        'quantity',
        'unit_price',
        'subtotal',
        'tax_rate_id',
        'tax_amount',
        'shipping_cost',
        'total_value',
        'payment_status',
        'export_date',
        'destination_port',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }
}
