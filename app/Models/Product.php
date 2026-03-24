<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'quantity', 'description'];

    protected $casts = [
        'price'    => 'decimal:2',
        'quantity' => 'integer',
    ];

    /** Ushbu mahsulot bo'yicha barcha sotuv elementlari */
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
