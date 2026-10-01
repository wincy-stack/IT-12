<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    protected $fillable = [
        'name',
        'category',
        'quantity',
        'minimum_stock',
        'unit_price',
        'unit',
        'description',
    ];

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->minimum_stock;
    }
}
