<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Thông số tổng quan
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalUsers = User::where('role', 'user')->count();
        
        // Doanh thu (Chỉ tính đơn đã hoàn thành)
        $totalRevenue = Order::where('status', 'Completed')->sum('total_price');
        
        // Tổng Lợi nhuận (Chỉ tính theo Giá bán ra trừ Giá gốc của từng sản phẩm)
        $totalProfit = \App\Models\OrderItem::whereHas('order', function($q) {
            $q->where('status', 'Completed');
        })->get()->sum(function($item) {
            return ($item->price - $item->cost_price) * $item->quantity;
        });

        // Doanh thu tháng này
        $thisMonthRevenue = Order::where('status', 'Completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_price');
            
        $thisMonthProfit = \App\Models\OrderItem::whereHas('order', function($q) {
            $q->where('status', 'Completed')
              ->whereMonth('created_at', Carbon::now()->month)
              ->whereYear('created_at', Carbon::now()->year);
        })->get()->sum(function($item) {
            return ($item->price - $item->cost_price) * $item->quantity;
        });

        // So sánh với tháng trước
        $lastMonthRevenue = Order::where('status', 'Completed')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('total_price');
            
        $lastMonthProfit = \App\Models\OrderItem::whereHas('order', function($q) {
            $q->where('status', 'Completed')
              ->whereMonth('created_at', Carbon::now()->subMonth()->month)
              ->whereYear('created_at', Carbon::now()->subMonth()->year);
        })->get()->sum(function($item) {
            return ($item->price - $item->cost_price) * $item->quantity;
        });
        
        $revenueGrowth = $lastMonthRevenue > 0 ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;
        $profitGrowth = $lastMonthProfit > 0 ? (($thisMonthProfit - $lastMonthProfit) / $lastMonthProfit) * 100 : 0;

        // Số lượng sản phẩm đang Flash Sale
        $totalFlashSales = Product::where('is_flash_sale', true)->count();

        // 5 đơn hàng gần đây
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        // Thống kê danh mục
        $categoryStats = Category::withSum('products', 'stock')
                                 ->withSum('products', 'sold_count')
                                 ->get();
        // Sắp xếp giảm dần theo số lượng đã bán
        $sortedCategories = $categoryStats->sortByDesc('products_sum_sold_count');
        $bestSellingCategory = $sortedCategories->first();

        // ------------------ NEW CHART DATA ------------------
        // Doanh thu 7 ngày gần nhất
        $dailyRevenueRaw = Order::where('status', 'Completed')
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->select(\Illuminate\Support\Facades\DB::raw('DATE(created_at) as date'), \Illuminate\Support\Facades\DB::raw('SUM(total_price) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $chartDayLabels = [];
        $chartDayData = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartDayLabels[] = Carbon::now()->subDays($i)->format('d/m');
            $match = $dailyRevenueRaw->firstWhere('date', $d);
            $chartDayData[] = $match ? (int)$match->total : 0;
        }

        // Doanh thu 12 tháng năm nay
        $monthlyRevenueRaw = Order::where('status', 'Completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->select(\Illuminate\Support\Facades\DB::raw('MONTH(created_at) as month'), \Illuminate\Support\Facades\DB::raw('SUM(total_price) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        $chartMonthLabels = [];
        $chartMonthData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartMonthLabels[] = 'Th ' . $i;
            $match = $monthlyRevenueRaw->firstWhere('month', $i);
            $chartMonthData[] = $match ? (int)$match->total : 0;
        }

        // Doanh thu theo năm
        $yearlyRevenueRaw = Order::where('status', 'Completed')
            ->select(\Illuminate\Support\Facades\DB::raw('YEAR(created_at) as year'), \Illuminate\Support\Facades\DB::raw('SUM(total_price) as total'))
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();
            
        $chartYearLabels = $yearlyRevenueRaw->pluck('year')->toArray();
        if(empty($chartYearLabels)) {
            $chartYearLabels = [Carbon::now()->year];
            $chartYearData = [0];
        } else {
            $chartYearData = $yearlyRevenueRaw->pluck('total')->map(function($v){ return (int)$v; })->toArray();
        }
        
        $chartDayLabels = json_encode($chartDayLabels);
        $chartDayData = json_encode($chartDayData);
        $chartMonthLabels = json_encode($chartMonthLabels);
        $chartMonthData = json_encode($chartMonthData);
        $chartYearLabels = json_encode($chartYearLabels);
        $chartYearData = json_encode($chartYearData);
        // ----------------------------------------------------

        // Chuẩn bị label và data cho Chart.js
        // Format lại giá trị null -> 0 để render chart ko bị lỗi
        $chartLabels = $categoryStats->pluck('name')->toJson();
        $chartStockData = $categoryStats->pluck('products_sum_stock')->map(function($v){ return (int)$v; })->toJson();
        $chartSoldData = $categoryStats->pluck('products_sum_sold_count')->map(function($v){ return (int)$v; })->toJson();

        return view('admin.dashboard', compact(
            'totalOrders', 
            'totalProducts', 
            'totalUsers', 
            'totalRevenue',
            'thisMonthRevenue',
            'revenueGrowth',
            'totalProfit',
            'thisMonthProfit',
            'profitGrowth',
            'totalFlashSales',
            'recentOrders',
            'categoryStats',
            'bestSellingCategory',
            'chartLabels',
            'chartStockData',
            'chartSoldData',
            'chartDayLabels',
            'chartDayData',
            'chartMonthLabels',
            'chartMonthData',
            'chartYearLabels',
            'chartYearData'
        ));
    }

    public function export(Request $request)
    {
        $categoryStats = Category::withSum('products', 'stock')
                                 ->withSum('products', 'sold_count')
                                 ->get();

        $view = view('admin.export_inventory', compact('categoryStats'))->render();

        $fileName = 'Bao_cao_ton_kho_thang_' . Carbon::now()->format('m-Y') . '.xls';

        return response($view)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->header('Expires', '0')
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Pragma', 'public');
    }
}
