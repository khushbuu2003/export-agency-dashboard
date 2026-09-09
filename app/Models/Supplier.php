<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_name',
        'company_name',
        'contact_person',
        'mobile',
        'address',
        'gst_pan',
        'cotton_type',
        'payment_terms',
        'payment_due_days',
        'purchase_date',
        'bank_details',
        'total_purchased',
        'total_paid',
    ];

    public function balanceDue()
    {
        return max(0, $this->total_purchased - $this->total_paid);
    }

    public function getDueDateAttribute()
    {
        $baseDate = $this->purchase_date ? Carbon::parse($this->purchase_date) : Carbon::parse($this->created_at);
        $days = (int) ($this->payment_due_days ?: 7);
        return $baseDate->copy()->addDays($days);
    }

    public function isOverdue()
    {
        if ($this->balanceDue() <= 0) {
            return false;
        }
        return Carbon::now()->startOfDay()->greaterThan($this->getDueDateAttribute()->startOfDay());
    }

    public function daysOverdue()
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return Carbon::now()->startOfDay()->diffInDays($this->getDueDateAttribute()->startOfDay());
    }
}
