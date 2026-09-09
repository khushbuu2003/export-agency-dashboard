<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_code',
        'stock_id',
        'qty',
        'sender_party',
        'receiver_party',
        'transfer_date',
        'status',
        'notes',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
