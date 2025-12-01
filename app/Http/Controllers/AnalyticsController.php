<?php

namespace App\Http\Controllers;

use App\Traits\ResponseTraits;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    use ResponseTraits;

    /**
     * @return JsonResponse
     */
    public function highVarietyCustomers()
    {
        try {
            $customers = DB::table('invoices')
                ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
                ->join('customers', 'customers.id', '=', 'invoices.customer_id')
                ->selectRaw('
                customers.id AS customer_id,
                customers.name AS customer_name,
                customers.email AS customer_email,
                DATE(invoices.created_at) AS purchase_date,

                COUNT(DISTINCT invoice_items.product_id) AS distinct_products,
                SUM(invoice_items.quantity) AS total_items_purchased,
                SUM(invoice_items.total_amount) AS total_amount_spent,
                SUM(invoice_items.tax_amount) AS total_tax_paid
            ')
                ->groupBy(
                    'customers.id',
                    'customers.name',
                    'customers.email',
                    DB::raw('DATE(invoices.created_at)')
                )
                ->having('distinct_products', '>=', 5)
                ->orderByDesc('total_amount_spent')
                ->limit(5)
                ->get();

            if (sizeof($customers) <= 0) {
                return $this->errorResponse(null, config('common.errors.not_found'));
            }

            return $this->successResponse($customers, config('common.success.simple_success'));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), config('common.errors.went_wrong'));
        }
    }

    /**
     * @return JsonResponse
     */
    public function stockForecast()
    {

        try {
            $fromDate = Carbon::now()->subDays(7)->startOfDay();

            $products = DB::table('products')
                ->leftJoin('invoice_items', 'invoice_items.product_id', '=', 'products.id')
                ->leftJoin('invoices', function ($join) use ($fromDate) {
                    $join->on('invoices.id', '=', 'invoice_items.invoice_id')
                        ->where('invoices.created_at', '>=', $fromDate);
                })
                ->selectRaw('
                products.id,
                products.name,
                products.available_stock,

                COALESCE(SUM(invoice_items.quantity), 0) AS total_sold_7_days
            ')
                ->groupBy('products.id', 'products.name', 'products.available_stock')
                ->get()
                ->map(function ($product) {
                    $avgDailySales = round($product->total_sold_7_days / 7, 2);

                    $estimatedDaysLeft = $avgDailySales > 0
                        ? floor($product->available_stock / $avgDailySales)
                        : null;

                    return [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'available_stock' => (int)$product->available_stock,
                        'avg_daily_sales' => $avgDailySales,
                        'estimated_days_left' => $estimatedDaysLeft, // null = not selling
                        'restock_alert' => $estimatedDaysLeft !== null && $estimatedDaysLeft <= 3
                    ];
                });

            if (sizeof($products) <= 0) {
                return $this->errorResponse(null, config('common.errors.not_found'));
            }
            return $this->successResponse($products, config('common.success.simple_success'));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), config('common.errors.went_wrong'));
        }
    }

    /**
     * @return JsonResponse
     */
    public function repeatCustomers()
    {
        try {
            $customers = DB::table('invoices')
                ->selectRaw('
                customer_id,
                created_at,
                total_amount,
                ROW_NUMBER() OVER (PARTITION BY customer_id ORDER BY created_at) AS purchase_order
            ')
                ->orderBy('created_at')
                ->get()
                ->groupBy('customer_id');

            //dd($customers);

            $result = [];
            foreach ($customers as $customerId => $purchases) {

                if ($purchases->count() < 2) {
                    continue;
                }

                $first = $purchases->firstWhere('purchase_order', 1);
                $second = $purchases->firstWhere('purchase_order', 2);

                if (!$first || !$second) {
                    continue;
                }

                $daysDiff = now()->parse($first->created_at)
                    ->diffInDays(now()->parse($second->created_at));

                if ($daysDiff > 7) {
                    continue;
                }

                $totalSpent = DB::table('invoices')
                    ->where('customer_id', $customerId)
                    ->whereBetween('created_at', [
                        $first->created_at,
                        $second->created_at
                    ])
                    ->sum('total_amount');

                $customerInfo = DB::table('customers')->where('id', $customerId)->first();
                $result[] = [
                    'customer_id' => $customerId,
                    'customer_name' => $customerInfo->name ?? null,
                    'customer_email' => $customerInfo->email ?? null,
                    'first_purchase' => $first->created_at,
                    'second_purchase' => $second->created_at,
                    'total_spent' => round($totalSpent, 2),
                    'days_between' => $daysDiff,
                ];
            }
            if (sizeof($result) <= 0) {
                return $this->errorResponse(null, config('common.errors.not_found'));
            }
            return $this->successResponse($result, config('common.success.simple_success'));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), config('common.errors.went_wrong'));
        }
    }

    public function highDemandOrders(){
        try {
            $fromDate = Carbon::now()->subDays(30);

            // Get Top 5 High-Demand Products (based on quantity sold)
            $topProducts = DB::table('invoice_items')
                ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->where('invoices.created_at', '>=', $fromDate)
                ->groupBy('invoice_items.product_id')
                ->orderByRaw('SUM(invoice_items.quantity) DESC')
                ->limit(5)
                ->pluck('invoice_items.product_id');

            if ($topProducts->isEmpty()) {
                return $this->errorResponse(null, config('common.errors.not_found'));
            }

            /// Fetch invoices that include any high-demand product
            $rows = DB::table('invoices')
                ->join('customers', 'invoices.customer_id', '=', 'customers.id')
                ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
                ->join('products', 'invoice_items.product_id', '=', 'products.id')
                ->whereIn('invoice_items.product_id', $topProducts)
                ->where('invoices.created_at', '>=', $fromDate)
                ->select(
                    'invoices.id as invoice_id',
                    'invoices.invoice_no',
                    'invoices.created_at as invoice_date',
                    'invoices.total_amount',

                    'customers.name as customer_name',
                    'customers.email as customer_email',

                    'products.name as product_name',
                    'invoice_items.quantity',
                    'invoice_items.price_per_unit',
                    'invoice_items.tax_amount',
                    'invoice_items.total_amount as line_total'
                )
                ->orderByDesc('invoices.created_at')
                ->get();

            // Group & format
            $result = $rows->groupBy('invoice_id')->map(function ($items, $invoiceId) {
                $first = $items->first();

                return [
                    'invoice_id'   => $invoiceId,
                    'invoice_no'   => $first->invoice_no,
                    'invoice_date' => $first->invoice_date,
                    'customer' => [
                        'name'  => $first->customer_name,
                        'email' => $first->customer_email,
                    ],
                    'total_amount' => $first->total_amount,
                    'high_demand_items' => $items->map(function ($item) {
                        return [
                            'product_name' => $item->product_name,
                            'quantity'     => $item->quantity,
                            'price_per_unit' => $item->price_per_unit,
                            'tax_amount'   => $item->tax_amount,
                            'line_total'   => $item->line_total
                        ];
                    })->values()
                ];
            })->values();
            //dd($result);


            if (sizeof($result) <= 0) {
                return $this->errorResponse(null, config('common.errors.not_found'));
            }
            return $this->successResponse($result, config('common.success.simple_success'));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), config('common.errors.went_wrong'));
        }
    }
}
