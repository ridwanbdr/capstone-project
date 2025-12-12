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

        // Production distribution for pie chart
        $productionDistribution = $this->getProductionDistribution();

        // Latest raw stock purchases (last 5)
        $latestRawStocks = $this->getLatestRawStocks();

        // Latest transactions (last 5)
        $latestTransactions = $this->getLatestTransactions();

        return view('dashboard.index', compact(
            'monthlyData',
            'totalSales',
            'productionDistribution',
            'latestRawStocks',
            'latestTransactions'
        ));
    }

    /**
     * Get monthly transaction data for the last 12 months
     */
    private function getMonthlyTransactionData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i)->startOfMonth();
            $monthName = $date->format('M');

            // Sum all transactions for this month
            $total = Transaction::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('total');

            $data[] = [
                'month' => $monthName,
                'total' => (int) $total,
            ];
        }
        return $data;
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

    /**
     * Get product distribution per production label for pie chart
     */
    private function getProductionDistribution()
    {
        $data = Production::withCount('detailProducts')
            ->get()
            ->map(function ($prod) {
                return [
                    'label' => $prod->production_label,
                    'count' => $prod->detail_products_count,
                ];
            })
            ->filter(function ($item) {
                return $item['count'] > 0;
            })
            ->values();

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
