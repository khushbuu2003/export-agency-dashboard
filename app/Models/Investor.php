<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company',
        'email',
        'phone',
        'investment_amount',
        'amount_received',
        'share_percentage',
        'status',
    ];

    public function pendingToReceive()
    {
        return max(0, $this->investment_amount - $this->amount_received);
    }

    public function hasPendingPayment()
    {
        return $this->pendingToReceive() > 0;
    }
}
