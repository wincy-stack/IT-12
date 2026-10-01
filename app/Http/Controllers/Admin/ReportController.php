<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Supply;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Parse date range filters (today, yesterday, this_week, this_month, last_month, this_year, all, custom)
     */
    protected function parseDateRange(Request $request): array
    {
        $period    = $request->get('period', 'this_month');
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        $now = Carbon::now();

        switch ($period) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end   = $now->copy()->endOfDay();
                $label = 'Today (' . $start->format('M d, Y') . ')';
                break;
            case 'yesterday':
                $start = $now->copy()->subDay()->startOfDay();
                $end   = $now->copy()->subDay()->endOfDay();
                $label = 'Yesterday (' . $start->format('M d, Y') . ')';
                break;
            case 'this_week':
                $start = $now->copy()->startOfWeek();
                $end   = $now->copy()->endOfWeek();
                $label = 'This Week (' . $start->format('M d') . ' – ' . $end->format('M d, Y') . ')';
                break;
            case 'this_month':
                $start = $now->copy()->startOfMonth();
                $end   = $now->copy()->endOfMonth();
                $label = 'This Month (' . $now->format('F Y') . ')';
                break;
            case 'last_month':
                $lastMonth = $now->copy()->subMonth();
                $start = $lastMonth->copy()->startOfMonth();
                $end   = $lastMonth->copy()->endOfMonth();
                $label = 'Last Month (' . $lastMonth->format('F Y') . ')';
                break;
            case 'this_year':
                $start = $now->copy()->startOfYear();
                $end   = $now->copy()->endOfYear();
                $label = 'This Year (' . $now->format('Y') . ')';
                break;
            case 'custom':
                $start = $startDate ? Carbon::parse($startDate)->startOfDay() : $now->copy()->subDays(30)->startOfDay();
                $end   = $endDate ? Carbon::parse($endDate)->endOfDay() : $now->copy()->endOfDay();
                $label = 'Custom (' . $start->format('M d, Y') . ' – ' . $end->format('M d, Y') . ')';
                break;
            case 'all':
            default:
                $start = null;
                $end   = null;
                $label = 'All Time Records';
                break;
        }

        return [$start, $end, $period, $label];
    }

    /**
     * Sales Report View & Analytics
     */
    public function sales(Request $request)
    {
        [$start, $end, $period, $periodLabel] = $this->parseDateRange($request);

        $paymentStatus = $request->get('payment_status', 'all');
        $orderStatus   = $request->get('order_status', 'all');
        $paymentMethod = $request->get('payment_method', 'all');

        $query = Order::with(['customer', 'staff', 'items']);

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        if ($paymentStatus !== 'all' && !empty($paymentStatus)) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($orderStatus !== 'all' && !empty($orderStatus)) {
            $query->where('status', $orderStatus);
        }

        if ($paymentMethod !== 'all' && !empty($paymentMethod)) {
            $query->where('payment_method', $paymentMethod);
        }

        // Summary Calculations
        $allMatching = (clone $query)->get();

        $totalRevenue       = (float) $allMatching->where('payment_status', 'paid')->sum('total_amount');
        $totalOrdersCount   = $allMatching->count();
        $paidOrdersCount    = $allMatching->where('payment_status', 'paid')->count();
        $unpaidOrdersCount  = $allMatching->where('payment_status', '!=', 'paid')->count();
        $unpaidAmount       = (float) $allMatching->where('payment_status', '!=', 'paid')->sum('total_amount');
        $grossSalesVolume   = (float) $allMatching->sum('total_amount');
        $avgOrderValue      = $totalOrdersCount > 0 ? ($grossSalesVolume / $totalOrdersCount) : 0;

        // Total containers/bottles sold
        $orderIds = $allMatching->pluck('id');
        $matchingItems = OrderItem::whereIn('order_id', $orderIds)->get();
        $totalUnitsSold = (int) $matchingItems->sum('quantity');

        // Breakdown by Container Size
        $containerSizes = Product::getContainerSizesArray();
        $salesBySize = [];
        foreach ($containerSizes as $key => $meta) {
            $itemsForSize = $matchingItems->where('container_size', $key);
            $qty = (int) $itemsForSize->sum('quantity');
            $subtotal = (float) $itemsForSize->sum('subtotal');
            $salesBySize[$key] = [
                'name'       => $meta['name'] ?? $key,
                'short_name' => $meta['short_name'] ?? $key,
                'quantity'   => $qty,
                'subtotal'   => $subtotal,
                'share'      => $grossSalesVolume > 0 ? round(($subtotal / $grossSalesVolume) * 100, 1) : 0,
                'icon'       => $meta['icon'] ?? 'fa-droplet',
            ];
        }

        // Breakdown by Service Type (Refill vs New Container)
        $refillSubtotal = (float) $matchingItems->where('service_type', 'refill')->sum('subtotal');
        $newContainerSubtotal = (float) $matchingItems->where('service_type', 'new_container')->sum('subtotal');

        $salesByService = [
            'refill' => [
                'label'    => 'Water Refill',
                'quantity' => (int) $matchingItems->where('service_type', 'refill')->sum('quantity'),
                'subtotal' => $refillSubtotal,
                'share'    => $grossSalesVolume > 0 ? round(($refillSubtotal / $grossSalesVolume) * 100, 1) : 0,
            ],
            'new_container' => [
                'label'    => 'New Container',
                'quantity' => (int) $matchingItems->where('service_type', 'new_container')->sum('quantity'),
                'subtotal' => $newContainerSubtotal,
                'share'    => $grossSalesVolume > 0 ? round(($newContainerSubtotal / $grossSalesVolume) * 100, 1) : 0,
            ],
        ];

        // Breakdown by Payment Method
        $paymentMethodsList = ['cash', 'gcash', 'bank_transfer', 'unspecified'];
        $salesByPaymentMethod = [];
        foreach ($paymentMethodsList as $method) {
            $ordersForMethod = $allMatching->filter(function ($o) use ($method) {
                if ($method === 'unspecified') {
                    return empty($o->payment_method);
                }
                return $o->payment_method === $method;
            });
            $salesByPaymentMethod[$method] = [
                'label'  => match ($method) {
                    'cash'          => 'Cash on Hand / Walk-in',
                    'gcash'         => 'GCash e-Wallet',
                    'bank_transfer' => 'Bank Transfer',
                    default         => 'Pending / Unset',
                },
                'count'  => $ordersForMethod->count(),
                'amount' => (float) $ordersForMethod->sum('total_amount'),
            ];
        }

        // Daily / Periodic Trend Data for Chart
        $trendData = $this->buildSalesTrend($allMatching, $start, $end, $period);

        // Paginated orders for the table view
        $orders = (clone $query)->latest()->paginate(15)->withQueryString();

        return view('admin.reports.sales', compact(
            'orders',
            'period',
            'periodLabel',
            'totalRevenue',
            'grossSalesVolume',
            'totalOrdersCount',
            'paidOrdersCount',
            'unpaidOrdersCount',
            'unpaidAmount',
            'totalUnitsSold',
            'avgOrderValue',
            'salesBySize',
            'salesByService',
            'salesByPaymentMethod',
            'trendData',
            'paymentStatus',
            'orderStatus',
            'paymentMethod'
        ));
    }

    /**
     * Income & Profitability Report
     */
    public function income(Request $request)
    {
        [$start, $end, $period, $periodLabel] = $this->parseDateRange($request);

        // Filter orders for income (only paid orders or all completed orders reflect realized income)
        $ordersQuery = Order::with('items')->where(function ($q) {
            $q->where('payment_status', 'paid')
              ->orWhere('status', 'completed');
        });

        if ($start && $end) {
            $ordersQuery->whereBetween('created_at', [$start, $end]);
        }

        $paidOrders = $ordersQuery->get();
        $orderIds   = $paidOrders->pluck('id');
        $items      = OrderItem::whereIn('order_id', $orderIds)->get();

        // 1. Gross Revenue
        $grossRevenue = (float) $paidOrders->sum('total_amount');

        // 2. Cost of Goods Sold (COGS)
        // Load product cost price mapping
        $products = Product::all()->keyBy('size_key');
        $defaultCosts = [
            '500ml'    => 1.50,
            '1_gallon' => 4.50,
            '5_gallon' => 8.00,
        ];

        $totalCOGS = 0.0;
        $profitByProduct = [];

        foreach ($items as $item) {
            $size = $item->container_size;
            $unitCost = 0.0;
            if (isset($products[$size]) && (float) $products[$size]->cost_price > 0) {
                $unitCost = (float) $products[$size]->cost_price;
            } elseif (isset($defaultCosts[$size])) {
                $unitCost = $defaultCosts[$size];
            }

            // If it's a new container purchase, factor container supply acquisition cost
            if ($item->service_type === 'new_container') {
                $unitCost += 120.00; // estimated container base cost
            }

            $lineCost = $unitCost * (int) $item->quantity;
            $lineRevenue = (float) $item->subtotal;
            $lineProfit = max(0, $lineRevenue - $lineCost);

            $totalCOGS += $lineCost;

            if (!isset($profitByProduct[$size])) {
                $profitByProduct[$size] = [
                    'name'         => $item->containerSizeLabel(),
                    'short_name'   => $item->containerSizeShort(),
                    'quantity'     => 0,
                    'revenue'      => 0.0,
                    'cost'         => 0.0,
                    'gross_profit' => 0.0,
                ];
            }

            $profitByProduct[$size]['quantity']     += (int) $item->quantity;
            $profitByProduct[$size]['revenue']      += $lineRevenue;
            $profitByProduct[$size]['cost']         += $lineCost;
            $profitByProduct[$size]['gross_profit'] += $lineProfit;
        }

        // Calculate margin for each product
        foreach ($profitByProduct as $k => $data) {
            $rev = $data['revenue'];
            $profitByProduct[$k]['margin'] = $rev > 0 ? round(($data['gross_profit'] / $rev) * 100, 1) : 0;
        }

        // 3. Operating & Supply Expenses
        // Calculate supply expenditures
        $suppliesValue = (float) Supply::all()->sum(function ($s) {
            return (float) $s->unit_price * (int) $s->quantity;
        });
        // Prorated operating/water filtration maintenance allocation for the period
        $operatingOverhead = round($grossRevenue * 0.12, 2); // 12% operational electricity/filter/maintenance benchmark
        $totalExpenses = round($totalCOGS + $operatingOverhead, 2);

        // 4. Gross Profit & Net Income
        $grossProfit = max(0, $grossRevenue - $totalCOGS);
        $grossMargin = $grossRevenue > 0 ? round(($grossProfit / $grossRevenue) * 100, 1) : 0;

        $netIncome   = $grossRevenue - $totalExpenses;
        $netMargin   = $grossRevenue > 0 ? round(($netIncome / $grossRevenue) * 100, 1) : 0;

        // Income Trend Chart Data
        $incomeTrend = $this->buildIncomeTrend($paidOrders, $start, $end, $period, $defaultCosts, $products);

        return view('admin.reports.income', compact(
            'period',
            'periodLabel',
            'grossRevenue',
            'totalCOGS',
            'operatingOverhead',
            'totalExpenses',
            'grossProfit',
            'grossMargin',
            'netIncome',
            'netMargin',
            'profitByProduct',
            'incomeTrend',
            'paidOrders'
        ));
    }

    /**
     * Helper to build sales trend dates and values
     */
    protected function buildSalesTrend($orders, $start, $end, string $period): array
    {
        $labels = [];
        $values = [];

        if ($period === 'today' || $period === 'yesterday') {
            // Group by 2-hour blocks
            for ($h = 6; $h <= 20; $h += 2) {
                $labels[] = sprintf('%02d:00', $h);
                $blockRev = $orders->filter(function ($o) use ($h) {
                    $hour = Carbon::parse($o->created_at)->hour;
                    return $hour >= $h && $hour < ($h + 2);
                })->sum('total_amount');
                $values[] = round((float) $blockRev, 2);
            }
        } elseif ($period === 'this_year') {
            // Group by Month
            for ($m = 1; $m <= 12; $m++) {
                $dt = Carbon::create(Carbon::now()->year, $m, 1);
                $labels[] = $dt->format('M');
                $monthRev = $orders->filter(function ($o) use ($m) {
                    return Carbon::parse($o->created_at)->month === $m;
                })->sum('total_amount');
                $values[] = round((float) $monthRev, 2);
            }
        } else {
            // Group by Day (last 7 to 30 days)
            $daysCount = 14;
            $currentDate = $start ? $start->copy() : Carbon::now()->subDays(13);
            $targetEnd = $end ? $end->copy() : Carbon::now();

            while ($currentDate <= $targetEnd && count($labels) <= 31) {
                $dayStr = $currentDate->format('M d');
                $labels[] = $dayStr;

                $dayRev = $orders->filter(function ($o) use ($currentDate) {
                    return Carbon::parse($o->created_at)->isSameDay($currentDate);
                })->sum('total_amount');

                $values[] = round((float) $dayRev, 2);
                $currentDate->addDay();
            }
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    /**
     * Helper to build Income trend (Revenue, COGS, Net Profit)
     */
    protected function buildIncomeTrend($orders, $start, $end, string $period, array $defaultCosts, $products): array
    {
        $labels   = [];
        $revenues = [];
        $expenses = [];
        $profits  = [];

        $days = 10;
        $currentDate = $start ? $start->copy() : Carbon::now()->subDays(9);
        $targetEnd = $end ? $end->copy() : Carbon::now();

        while ($currentDate <= $targetEnd && count($labels) <= 31) {
            $labels[] = $currentDate->format('M d');

            $dayOrders = $orders->filter(function ($o) use ($currentDate) {
                return Carbon::parse($o->created_at)->isSameDay($currentDate);
            });

            $dayRev = (float) $dayOrders->sum('total_amount');
            $dayCost = 0.0;

            foreach ($dayOrders as $order) {
                foreach ($order->items as $item) {
                    $sz = $item->container_size;
                    $cost = isset($products[$sz]) ? (float) $products[$sz]->cost_price : ($defaultCosts[$sz] ?? 8.0);
                    $dayCost += ($cost * (int) $item->quantity);
                }
            }

            $dayOverhead = round($dayRev * 0.12, 2);
            $dayExp = round($dayCost + $dayOverhead, 2);
            $dayProfit = round($dayRev - $dayExp, 2);

            $revenues[] = round($dayRev, 2);
            $expenses[] = $dayExp;
            $profits[]  = $dayProfit;

            $currentDate->addDay();
        }

        return [
            'labels'   => $labels,
            'revenues' => $revenues,
            'expenses' => $expenses,
            'profits'  => $profits,
        ];
    }
}
