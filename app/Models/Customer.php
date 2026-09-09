<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'country',
        'tax_id',
        'address',
    ];

    public function exportTransactions()
    {
        return $this->hasMany(ExportTransaction::class);
    }
}
