<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'customer_id',
        'total_price',
        'sale_date',
        'payment_method',
        'status',
        'paid_amount',
        'due_date',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'sale_date'   => 'date:Y-m-d',
        'due_date'    => 'date:Y-m-d',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class)->orderBy('payment_date');
    }

    /**
     * Qolgan qarz miqdori (to'lanmagan summa)
     */
    public function getRemainingDebtAttribute(): float
    {
        return max(0.0, (float) $this->total_price - (float) $this->paid_amount);
    }
}
