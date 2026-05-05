<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'user')->withCount('orders')->withSum('orders', 'total_price')->with('orders', function($q) {
            $q->latest()->limit(1);
        });

        // Xử lý bộ lọc filter
        if ($request->has('filter')) {
            $filter = $request->filter;
            $now = Carbon::now();
            
            if ($filter == 'vip') {
                $query->having('orders_sum_total_price', '>', 5000000)->orHaving('orders_count', '>=', 5);
            } elseif ($filter == 'new') {
                $query->where('created_at', '>=', $now->subDays(30));
            } elseif ($filter == 'potential') {
                $query->having('orders_count', '=', 0);
            } elseif ($filter == 'inactive') {
                // Có đơn hàng nhưng đơn cuối cùng > 30 ngày
                $query->having('orders_count', '>', 0)->whereDoesntHave('orders', function($q) use ($now) {
                    $q->where('created_at', '>=', $now->subDays(30));
                });
            }
        }

        $customers = $query->paginate(20);
        $filter = $request->filter ?? 'all';

        return view('admin.customers.index', compact('customers', 'filter'));
    }
}
