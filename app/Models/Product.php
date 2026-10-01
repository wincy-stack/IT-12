<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'size_key',
        'category',
        'price',
        'cost_price',
        'stock',
        'unit',
        'icon',
        'description',
        'is_active',
    ];

    protected $casts = [
        'price'      => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock'      => 'integer',
        'is_active'  => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Profit per unit (Selling price - Cost price)
     */
    public function unitProfit(): float
    {
        return max(0, (float) $this->price - (float) $this->cost_price);
    }

    /**
     * Profit margin percentage
     */
    public function profitMarginPercent(): float
    {
        $price = (float) $this->price;
        if ($price <= 0) {
            return 0.0;
        }

        return round((($price - (float) $this->cost_price) / $price) * 100, 1);
    }

    /**
     * Get dynamic container sizes array formatted for Order system.
     * Maps database products having a size_key to the format expected by views and controllers.
     */
    public static function getContainerSizesArray(): array
    {
        try {
            $products = self::where('is_active', true)
                ->whereNotNull('size_key')
                ->get();

            if ($products->isNotEmpty()) {
                $sizes = [];
                foreach ($products as $p) {
                    $sizes[$p->size_key] = [
                        'key'         => $p->size_key,
                        'name'        => $p->name,
                        'short_name'  => self::generateShortName($p->size_key, $p->name),
                        'price'       => (float) $p->price,
                        'cost_price'  => (float) $p->cost_price,
                        'description' => $p->description ?? 'Water refilling service',
                        'icon'        => $p->icon ?: 'fa-droplet',
                        'stock'       => (int) $p->stock,
                    ];
                }
                return $sizes;
            }
        } catch (\Throwable $e) {
            // Fallback if table not ready
        }

        return Order::CONTAINER_SIZES;
    }

    public static function generateShortName(string $key, string $name): string
    {
        return match ($key) {
            '500ml'    => '500mL',
            '1_gallon' => '1-Gallon',
            '5_gallon' => '5-Gallon',
            default    => $name,
        };
    }
}
