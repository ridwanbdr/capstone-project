<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\DetailProduct;
use App\Models\Production;
use App\Models\RawStock;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard with various metrics and charts.
     */
    public function index()
    {
        // Monthly sales data for bar chart (last 12 months)
        $monthlyData = $this->getMonthlyTransactionData();

        // Total sales scorecard
        $totalSales = $this->getTotalSales();

        $totalQuantityProductSold = $this->getTotalQuantityProductSold();

        // Production distribution for pie chart
        $productionDistribution = $this->getProductionDistribution();

        // Latest raw stock purchases (last 5)
        $latestRawStocks = $this->getLatestRawStocks();

        // Latest transactions (last 5)
        $latestTransactions = $this->getLatestTransactions();

        return view('dashboard.index', compact(
            'monthlyData',
            'totalSales',
            'totalQuantityProductSold',
            'productionDistribution',
            'latestRawStocks',
            'latestTransactions'
        ));
    }

    /**
     * Get monthly transaction data grouped by year and month
     * Returns data for multiple years if available
     */
    private function getMonthlyTransactionData()
    {
        // Get all unique year-month combinations from transactions
        $yearMonths = Transaction::selectRaw('YEAR(date) as year, MONTH(date) as month')
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(24) // Last 24 months
            ->get()
            ->reverse();

        // Group by year
        $groupedByYear = [];
        foreach ($yearMonths as $ym) {
            $year = $ym->year;
            $month = $ym->month;
            
            if (!isset($groupedByYear[$year])) {
                $groupedByYear[$year] = [];
            }
            
            $date = Carbon::create($year, $month, 1);
            $monthName = $date->format('M');
            
            $total = Transaction::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->sum('total');
            
            $groupedByYear[$year][] = [
                'month' => $monthName,
                'monthNum' => $month,
                'total' => (int) $total,
            ];
        }

        // Format for chart (group by year)
        $years = array_keys($groupedByYear);
        $allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        // If no data, return empty structure
        if (empty($years)) {
            return [
                'categories' => $allMonths,
                'series' => [],
                'years' => [],
            ];
        }
        
        $series = [];
        foreach ($years as $year) {
            $yearData = [];
            foreach ($allMonths as $index => $monthName) {
                $monthNum = $index + 1;
                $found = collect($groupedByYear[$year])->firstWhere('monthNum', $monthNum);
                $yearData[] = $found ? $found['total'] : 0;
            }
            $series[] = [
                'name' => (string)$year,
                'data' => $yearData,
            ];
        }

        return [
            'categories' => $allMonths,
            'series' => $series,
            'years' => $years,
        ];
    }

    /**
     * Get total sales amount
     */
    private function getTotalSales()
    {
        $total = Transaction::sum('total') ?? 0;
        return [
            'amount' => (int) $total,
            'count' => Transaction::count(),
            'formatted' => 'Rp ' . number_format($total, 0, ',', '.'),
        ];
    }

    // Sum Quantity Product Sold
    private function getTotalQuantityProductSold()
    {
        $total_qty = Transaction::sum('qty') ?? 0;
        return [
            'qty' => (int) $total_qty,
            'formatted' => number_format($total_qty, 0, ',', '.') . ' Produk',
        ];
    }

    /**
     * Get best-selling products distribution for pie chart
     * Based on transaction data (product_name from transactions)
     */
    private function getProductionDistribution()
    {
        // Get sales data grouped by product_name from transactions
        $productSales = Transaction::selectRaw('product_name, SUM(total) as total_sales, SUM(qty) as total_qty, COUNT(*) as transaction_count')
            ->whereNotNull('product_name')
            ->where('product_name', '!=', '')
            ->groupBy('product_name')
            ->orderBy('total_sales', 'desc')
            ->limit(10) // Top 10 best-selling products
            ->get();

        $data = $productSales->map(function ($item) {
            return [
                'label' => $item->product_name,
                'sales' => (int) $item->total_sales,
                'qty' => (int) $item->total_qty,
                'count' => (int) $item->transaction_count,
            ];
        })->filter(function ($item) {
            return $item['sales'] > 0;
        })->values();

        return $data;
    }

    /**
     * Get latest 4 raw stock purchases
     */
    private function getLatestRawStocks()
    {
        return RawStock::orderBy('added_on', 'desc')
            ->take(4)
            ->get()
            ->map(function ($stock) {
                return [
                    'id' => $stock->material_id,
                    'name' => $stock->material_name,
                    'qty' => $stock->material_qty,
                    'price' => $stock->unit_price,
                    'total' => $stock->material_qty * $stock->unit_price,
                    'date' => Carbon::parse($stock->added_on)->format('d/m/Y'),
                    'formatted_price' => 'Rp ' . number_format($stock->unit_price, 0, ',', '.'),
                    'formatted_total' => 'Rp ' . number_format($stock->material_qty * $stock->unit_price, 0, ',', '.'),
                ];
            });
    }

    /**
     * Get latest 5 transactions for timeline
     */
    private function getLatestTransactions()
    {
        return Transaction::orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($trans) {
                // determine status based on paid vs total
                $statusColor = $trans->paid >= $trans->total ? 'success' : 'warning';
                $statusText = $trans->paid >= $trans->total ? 'Lunas' : 'Belum Lunas';
                
                $createdAt = Carbon::parse($trans->created_at);
                
                return [
                    'id' => $trans->transaction_id,
                    'date' => $createdAt->format('d/m/Y'),
                    'time' => $createdAt->format('H:i'),
                    'total' => 'Rp ' . number_format($trans->total, 0, ',', '.'),
                    'status' => $statusText,
                    'status_color' => $statusColor,
                    'method' => $trans->payment_method ?? '-',
                    'cart_total' => $trans->total,
                ];
            });
    }
}
