<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'container_size',
        'service_type',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
        'quantity'   => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function containerSizeLabel(): string
    {
        return Order::CONTAINER_SIZES[$this->container_size]['name'] ?? '5-Gallon Container';
    }

    public function containerSizeShort(): string
    {
        return Order::CONTAINER_SIZES[$this->container_size]['short_name'] ?? '5-Gal';
    }

    public function containerSizeIcon(): string
    {
        return Order::CONTAINER_SIZES[$this->container_size]['icon'] ?? 'fa-droplet';
    }

    public function serviceTypeLabel(): string
    {
        return Order::SERVICE_TYPES[$this->service_type]['label'] ?? 'Water Refill';
    }

    public function serviceTypeColor(): string
    {
        return Order::SERVICE_TYPES[$this->service_type]['color'] ?? '#0ea5e9';
    }

    public function serviceTypeIcon(): string
    {
        return Order::SERVICE_TYPES[$this->service_type]['icon'] ?? 'fa-arrows-rotate';
    }
}
