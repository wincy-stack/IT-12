<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    public const CONTAINER_SIZES = [
        '500ml' => [
            'key'         => '500ml',
            'name'        => '500mL Bottle',
            'short_name'  => '500mL',
            'price'       => 5.00,
            'description' => 'Compact personal drinking bottle',
            'icon'        => 'fa-bottle-water',
        ],
        '1_gallon' => [
            'key'         => '1_gallon',
            'name'        => '1-Gallon Bottle',
            'short_name'  => '1-Gallon',
            'price'       => 15.00,
            'description' => 'Ideal for personal & small family use',
            'icon'        => 'fa-jug-detergent',
        ],
        '5_gallon' => [
            'key'         => '5_gallon',
            'name'        => '5-Gallon Container',
            'short_name'  => '5-Gallon',
            'price'       => 30.00,
            'description' => 'Standard dispenser-ready water container',
            'icon'        => 'fa-bucket',
        ],
    ];

    public const SERVICE_TYPES = [
        'refill' => [
            'key'         => 'refill',
            'label'       => 'Water Refill',
            'description' => 'Refill your existing clean container',
            'icon'        => 'fa-arrows-rotate',
            'color'       => '#0ea5e9',
        ],
        'new_container' => [
            'key'         => 'new_container',
            'label'       => 'New Container Purchase',
            'description' => 'Brand new container filled with purified water',
            'icon'        => 'fa-box-open',
            'color'       => '#10b981',
        ],
    ];

    protected $fillable = [
        'customer_id',
        'staff_id',
        'order_number',
        'status',
        'payment_status',
        'payment_method',
        'paid_at',
        'released_at',
        'container_size',
        'service_type',
        'quantity',
        'unit_price',
        'gallons',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'unit_price'   => 'decimal:2',
        'quantity'     => 'integer',
        'gallons'      => 'integer',
        'paid_at'      => 'datetime',
        'released_at'  => 'datetime',
    ];

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isReleased(): bool
    {
        return $this->status === 'completed' || !is_null($this->released_at);
    }

    /**
     * Generate a guaranteed unique, sequential order number (e.g. ORD-0001, ORD-0002).
     */
    public static function generateOrderNumber(): string
    {
        $maxNum = self::pluck('order_number')
            ->map(function ($num) {
                if (preg_match('/(?:ORD-)?(\d+)/i', $num, $matches)) {
                    return (int) $matches[1];
                }
                return 0;
            })
            ->max() ?? 0;

        $next = $maxNum + 1;

        do {
            $candidate = 'ORD-' . str_pad($next, 4, '0', STR_PAD_LEFT);
            $next++;
        } while (self::where('order_number', $candidate)->exists());

        return $candidate;
    }

    public static function getPriceForSize(?string $size): float
    {
        if ($size) {
            try {
                $pPrice = Product::where('size_key', $size)->where('is_active', true)->value('price');
                if ($pPrice !== null) {
                    return (float) $pPrice;
                }
            } catch (\Throwable $e) {
                // Table might not exist or error
            }
        }
        return self::CONTAINER_SIZES[$size]['price'] ?? 30.00;
    }

    /** Returns the unit price of the order (used in fallback display when no items relation loaded) */
    public function unitPrice(): float
    {
        return (float) ($this->unit_price ?? self::getPriceForSize($this->container_size));
    }

    public function containerSizeLabel(): string
    {
        return self::CONTAINER_SIZES[$this->container_size]['name'] ?? '5-Gallon Container';
    }

    public function containerSizeShort(): string
    {
        return self::CONTAINER_SIZES[$this->container_size]['short_name'] ?? '5-Gal';
    }

    public function containerSizeIcon(): string
    {
        return self::CONTAINER_SIZES[$this->container_size]['icon'] ?? 'fa-droplet';
    }

    public function serviceTypeLabel(): string
    {
        return self::SERVICE_TYPES[$this->service_type]['label'] ?? 'Water Refill';
    }

    public function serviceTypeColor(): string
    {
        return self::SERVICE_TYPES[$this->service_type]['color'] ?? '#0ea5e9';
    }

    public function serviceTypeIcon(): string
    {
        return self::SERVICE_TYPES[$this->service_type]['icon'] ?? 'fa-arrows-rotate';
    }

    /** Status badge color helper */
    public function statusColor(): string
    {
        return match ($this->status) {
            'pending'   => '#fbbf24',
            'confirmed' => '#38bdf8',
            'completed' => '#34d399',
            'cancelled' => '#f87171',
            default     => '#94a3b8',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'   => 'Pending',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default     => ucfirst($this->status),
        };
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Returns formatted summary of items, e.g. "2 × 500mL, 3 × 1-Gal" */
    public function itemsSummary(): string
    {
        if ($this->relationLoaded('items') ? $this->items->isNotEmpty() : $this->items()->exists()) {
            return $this->items->map(function ($item) {
                return "{$item->quantity} × " . $item->containerSizeShort();
            })->join(', ');
        }

        // Fallback for older orders without order_items
        $qty = $this->quantity ?: ($this->gallons ?: 1);
        return "{$qty} × " . $this->containerSizeShort();
    }

    /** Returns formatted container summary like "5-Gal Container × 1" */
    public function containerSummary(): string
    {
        if ($this->relationLoaded('items') ? $this->items->isNotEmpty() : $this->items()->exists()) {
            return $this->items->map(function ($item) {
                return $item->containerSizeShort() . " Container × {$item->quantity}";
            })->join(', ');
        }

        $qty = $this->quantity ?: ($this->gallons ?: 1);
        return $this->containerSizeShort() . " Container × {$qty}";
    }

    /** Returns formatted service summary like "Water Refill" */
    public function serviceSummary(): string
    {
        if ($this->relationLoaded('items') ? $this->items->isNotEmpty() : $this->items()->exists()) {
            return $this->items->map(function ($item) {
                return $item->serviceTypeLabel();
            })->unique()->join(', ');
        }

        return $this->serviceTypeLabel();
    }

    /** Returns formatted summary of service types */
    public function serviceTypesSummary(): string
    {
        if ($this->relationLoaded('items') ? $this->items->isNotEmpty() : $this->items()->exists()) {
            $types = $this->items->map(fn($item) => $item->serviceTypeLabel())->unique();
            return $types->join(' & ');
        }

        return $this->serviceTypeLabel();
    }

    public function totalQuantity(): int
    {
        if ($this->relationLoaded('items') ? $this->items->isNotEmpty() : $this->items()->exists()) {
            return (int) $this->items->sum('quantity');
        }

        return (int) ($this->quantity ?: ($this->gallons ?: 1));
    }
}
