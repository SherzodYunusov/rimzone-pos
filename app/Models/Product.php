<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'cost_price', 'quantity', 'unit', 'unit_type', 'unit_value', 'description'];

    protected $casts = [
        'price'      => 'decimal:2',
        'cost_price' => 'decimal:2',
        'unit_value' => 'decimal:3',
        'quantity'   => 'decimal:3',
    ];

    /** Ushbu mahsulot bo'yicha barcha sotuv elementlari */
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
