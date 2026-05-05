<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Validate existing vouchers
        if (session()->has('vouchers')) {
            $vouchersSession = session('vouchers');
            foreach ($vouchersSession as $key => $v) {
                if ($total < $v['min_order_value']) {
                    unset($vouchersSession[$key]);
                }
            }
            session()->put('vouchers', array_values($vouchersSession));
        }

        // Auto-apply best public voucher if no voucher applied
        if ((!session()->has('vouchers') || count(session('vouchers')) === 0) && $total > 0 && !session()->has('auto_voucher_disabled')) {
            $vouchers = \App\Models\Voucher::where('min_order_value', '>', 0)
                ->where(function($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', \Carbon\Carbon::now());
                })
                ->where(function($q) {
                    $q->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
                })
                ->get();
                
            if ($vouchers->isEmpty()) {
                $vouchers = collect([
                    (object)['id' => null, 'code' => 'FREESHIP', 'type' => 'freeship', 'min_order_value' => 150000, 'discount_value' => 30000, 'max_discount' => 0],
                    (object)['id' => null, 'code' => 'DISCOUNT10', 'type' => 'percent', 'min_order_value' => 300000, 'discount_value' => 10, 'max_discount' => 50000],
                    (object)['id' => null, 'code' => 'DISCOUNT20', 'type' => 'percent', 'min_order_value' => 500000, 'discount_value' => 20, 'max_discount' => 100000],
                ]);
            }
                
            $bestDiscount = 0;
            $bestVoucher = null;
            
            foreach($vouchers as $v) {
                if ($total >= $v->min_order_value) {
                    $d = 0;
                    if ($v->type == 'percent') {
                        $d = ($total * $v->discount_value) / 100;
                        if ($v->max_discount > 0 && $d > $v->max_discount) $d = $v->max_discount;
                    } elseif ($v->type == 'amount') {
                        $d = $v->discount_value;
                    } elseif ($v->type == 'freeship') {
                        $d = $v->discount_value > 0 ? $v->discount_value : 30000; // Standard shipping fee assumption
                        if ($v->max_discount > 0 && $d > $v->max_discount) $d = $v->max_discount;
                        $d = min(30000, $d);
                    }
                    
                    if ($d > $bestDiscount) {
                        $bestDiscount = $d;
                        $bestVoucher = $v;
                    }
                }
            }
            
            if ($bestVoucher) {
                session()->put('vouchers', [[
                    'id' => $bestVoucher->id,
                    'code' => $bestVoucher->code,
                    'type' => $bestVoucher->type,
                    'discount_value' => $bestVoucher->discount_value,
                    'max_discount' => $bestVoucher->max_discount,
                    'min_order_value' => $bestVoucher->min_order_value,
                ]]);
            }
        }

        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "id" => $product->id,
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->sale_price ?: $product->price,
                "image" => $product->image
            ];
        }

        session()->put('cart', $cart);
        session()->flash('show_cart_drawer', true);
        return redirect()->back();
    }

    public function update(Request $request)
    {
        if($request->id && $request->quantity){
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
        }
        session()->flash('show_cart_drawer', true);
        return redirect()->back();
    }

    public function remove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
        }
        session()->flash('show_cart_drawer', true);
        return redirect()->back();
    }

    public function applyVoucher(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $voucher = \App\Models\Voucher::where('code', $request->code)->first();

        if (!$voucher) {
            return redirect()->back()->with('error', 'Mã giảm giá không tồn tại!');
        }

        if ($voucher->expires_at && \Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($voucher->expires_at))) {
            return redirect()->back()->with('error', 'Mã giảm giá đã hết hạn!');
        }

        $vouchersSession = session()->get('vouchers', []);
        foreach ($vouchersSession as $v) {
            if ($v['code'] === $voucher->code) {
                return redirect()->back()->with('error', 'Mã giảm giá này đã được áp dụng!');
            }
        }

        if ($voucher->usage_limit && $voucher->used_count >= $voucher->usage_limit) {
            return redirect()->back()->with('error', 'Mã giảm giá đã hết lượt sử dụng!');
        }

        // Check if cart total >= min_order_value
        $cart = session()->get('cart', []);
        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        if ($total < $voucher->min_order_value) {
            return redirect()->back()->with('error', 'Đơn hàng chưa đạt yêu cầu tối thiểu ('.number_format($voucher->min_order_value).' đ) để áp dụng mã này!');
        }

        if ($voucher->is_for_new_user) {
            if (!\Illuminate\Support\Facades\Auth::check()) {
                return redirect()->back()->with('error', 'Mã đặc quyền này dành cho Tân thủ, vui lòng đăng nhập để sử dụng!');
            }
            $userId = \Illuminate\Support\Facades\Auth::id();
            $orderCount = \App\Models\Order::where('user_id', $userId)->count();
            if ($orderCount > 0) {
                return redirect()->back()->with('error', 'Mã này chỉ dành cho khách hàng chưa từng mua hàng trên hệ thống!');
            }
        }

        $vouchersSession[] = [
            'id' => $voucher->id,
            'code' => $voucher->code,
            'type' => $voucher->type,
            'discount_value' => $voucher->discount_value,
            'max_discount' => $voucher->max_discount,
            'min_order_value' => $voucher->min_order_value,
        ];
        session()->put('vouchers', $vouchersSession);
        session()->forget('auto_voucher_disabled');
        return redirect()->route('cart.index')->with('success', 'Đã áp dụng mã giảm giá thành công!');
    }

    public function removeVoucher(Request $request)
    {
        $code = $request->input('code');
        $vouchersSession = session()->get('vouchers', []);
        foreach ($vouchersSession as $key => $v) {
            if ($v['code'] === $code) {
                unset($vouchersSession[$key]);
            }
        }
        session()->put('vouchers', array_values($vouchersSession));
        
        // Vô hiệu hóa việc tự động áp dụng lại mã khi người dùng đã chủ động hủy
        session()->put('auto_voucher_disabled', true);
        
        return redirect()->route('cart.index')->with('success', 'Đã hủy mã giảm giá!');
    }
}
