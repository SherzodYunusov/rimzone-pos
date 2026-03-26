<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
        'company_name',
        'photo',
        'lat',
        'lng',
        'map_link',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
