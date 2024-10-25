<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'payment_id',
        'product_name',
        'quantity',
        'total_amount',
        'currency',
        'payer_email',
        'payer_name',
        'payment_status',
        'payment_method'
    ];
}
