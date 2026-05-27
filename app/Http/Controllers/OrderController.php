<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->with('orderItems.product')->latest()->get();
        return view('orders.index', compact('orders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
        ]);

        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('home')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Tính toán phí vận chuyển và áp dụng voucher nếu có
        $shippingFee = 30000;
        $discountAmount = 0;
        $shippingDiscount = 0;

        $appliedCodes = [];
        if (session()->has('vouchers')) {
            $vouchersSession = session('vouchers');
            foreach ($vouchersSession as $key => $voucher) {
                $vMinOrderValue = data_get($voucher, 'min_order_value', 0);
                
                if ($total >= $vMinOrderValue) {
                    $voucherId = data_get($voucher, 'id');

                    $dbVoucher = \App\Models\Voucher::find($voucherId);
                    if ($dbVoucher) {
                        $dbVoucher->used_count += 1;
                        $dbVoucher->save();
                    }

                    $vType = data_get($voucher, 'type', 'percent');
                    $vValue = data_get($voucher, 'discount_value', 0);
                    $vMax = data_get($voucher, 'max_discount', 0);

                    if ($vType == 'percent') {
                        $d = ($total * $vValue) / 100;
                        if ($vMax > 0 && $d > $vMax) $d = $vMax;
                        $discountAmount += $d;
                    } elseif ($vType == 'amount') {
                        $discountAmount += $vValue;
                    } elseif ($vType == 'freeship') {
                        $d = $vValue > 0 ? $vValue : $shippingFee;
                        if ($vMax > 0 && $d > $vMax) $d = $vMax;
                        $shippingDiscount += $d;
                    }
                    $appliedCodes[] = data_get($voucher, 'code');
                } else {
                    unset($vouchersSession[$key]);
                }
            }
            session()->put('vouchers', array_values($vouchersSession));
        }

        $shippingDiscount = min($shippingFee, $shippingDiscount);
        $discountAmount = min($total, $discountAmount);

        $finalTotal = $total + $shippingFee - $discountAmount - $shippingDiscount;
        if ($finalTotal < 0) $finalTotal = 0;

        $voucherCode = count($appliedCodes) > 0 ? implode(', ', $appliedCodes) : null;

        // Tạo Order với đầy đủ thông tin
        $paymentMethod = $request->input('payment_method', 'cod');
        
        $order = Order::create([
            'user_id' => Auth::id(),
            'subtotal' => $total,
            'shipping_fee' => $shippingFee,
            'discount_amount' => $discountAmount + $shippingDiscount,
            'voucher_code' => $voucherCode,
            'total_price' => $finalTotal,
            'status' => 'Pending',
            'payment_method' => $paymentMethod,
            'payment_status' => 'pending',
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'shipping_address' => $request->shipping_address,
        ]);

        // Tạo OrderItems và trừ Tồn Kho
        foreach($cart as $id => $item) {
            $product = Product::find($id);
            if($product) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'cost_price' => $product->cost_price ?? 0
                ]);

                // Trừ tồn kho và tăng lượt bán
                $product->stock -= $item['quantity'];
                $product->sold_count = ($product->sold_count ?? 0) + $item['quantity'];
                $product->save();
            }
        }

        // Xóa giỏ hàng và voucher
        session()->forget('cart');
        session()->forget('vouchers');

        // Logic tặng Voucher tự động (nếu KH đăng nhập)
        $rewardedVouchers = [];
        if(auth()->check()) {
            $user = auth()->user();
            $eligibleVouchers = \App\Models\Voucher::whereNotNull('reward_condition_amount')
                ->where('reward_condition_amount', '<=', $order->total_price)
                ->where(function($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', \Carbon\Carbon::now());
                })
                ->get();
                
            foreach($eligibleVouchers as $v) {
                // Kiểm tra chưa nhận
                $alreadyHas = \Illuminate\Support\Facades\DB::table('user_voucher')
                    ->where('user_id', $user->id)
                    ->where('voucher_id', $v->id)
                    ->exists();

                if(!$alreadyHas) {
                    \Illuminate\Support\Facades\DB::table('user_voucher')->insert([
                        'user_id' => $user->id,
                        'voucher_id' => $v->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $rewardedVouchers[] = $v;
                }
            }
        }

        if(count($rewardedVouchers) > 0) {
            session()->flash('rewarded_vouchers', collect($rewardedVouchers)->map(function($v) {
                return [
                    'code' => $v->code,
                    'type' => $v->type,
                    'discount_value' => $v->discount_value
                ];
            })->toArray());
        }

        if ($paymentMethod === 'vnpay' || $paymentMethod === 'vnpay_qr') {
            $vnp_Url = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
            $vnp_Returnurl = url('/vnpay/return');
            $vnp_TmnCode = 'CGXZLS0Z';
            $vnp_HashSecret = 'XNBQQMACUEKLOUOUVDPMHOHVEABHXXEI';

            // Đảm bảo mã giao dịch luôn là duy nhất bằng cách nối thêm timestamp (VNPay chỉ cho phép a-z, A-Z, 0-9)
            $vnp_TxnRef = $order->id . time();
            $vnp_OrderInfo = 'Thanh toan don hang ' . $order->id;
            $vnp_OrderType = 'billpayment';
            $vnp_Amount = $order->total_price * 100;
            $vnp_Locale = 'vn';
            $vnp_IpAddr = $request->ip();

            $timezone = new \DateTimeZone('Asia/Ho_Chi_Minh');
            $startTime = new \DateTime('now', $timezone);
            $expireTime = clone $startTime;
            $expireTime->modify('+15 minutes');

            $vnp_CreateDate = $startTime->format('YmdHis');
            $vnp_ExpireDate = $expireTime->format('YmdHis');

            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => intval($order->total_price * 100),
                "vnp_Command" => "pay",
                "vnp_CreateDate" => $vnp_CreateDate,
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
                "vnp_ExpireDate" => $vnp_ExpireDate
            );

            if ($paymentMethod === 'vnpay_qr') {
                $inputData['vnp_BankCode'] = 'VNPAYQR';
            }

            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . rawurlencode($key) . "=" . rawurlencode($value);
                } else {
                    $hashdata .= rawurlencode($key) . "=" . rawurlencode($value);
                    $i = 1;
                }
                $query .= rawurlencode($key) . "=" . rawurlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . rtrim($query, '&');
            if (!empty($vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                $vnp_Url .= '&vnp_SecureHash=' . $vnpSecureHash;
            }

            return redirect($vnp_Url);
        }

        return redirect()->route('home')->with('success', 'Đặt hàng thành công!');
    }

    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = 'XNBQQMACUEKLOUOUVDPMHOHVEABHXXEI';
        $vnp_SecureHash = $request->vnp_SecureHash;
        $inputData = array();
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        unset($inputData['vnp_SecureHash']);
        if (isset($inputData['vnp_SecureHashType'])) {
            unset($inputData['vnp_SecureHashType']);
        }
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . rawurlencode($key) . "=" . rawurlencode($value);
            } else {
                $hashData = $hashData . rawurlencode($key) . "=" . rawurlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        // Tách lấy Order ID (do vnp_TxnRef = order_id + timestamp 10 số)
        $txnRef = $inputData['vnp_TxnRef'];
        $orderId = substr($txnRef, 0, -10);
        $order = Order::find($orderId);

        if ($secureHash == $vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {
                if ($order) {
                    $order->payment_status = 'paid';
                    $order->status = 'Processing';
                    $order->transaction_id = $request->vnp_TransactionNo;
                    $order->save();
                }
                return redirect()->route('home')->with('success', 'Thanh toán qua VNPay thành công! Đơn hàng của bạn đang được xử lý.');
            } else {
                if ($order) {
                    $order->payment_status = 'failed';
                    $order->save();
                }
                return redirect()->route('home')->with('error', 'Thanh toán VNPay bị hủy hoặc không thành công.');
            }
        } else {
            return redirect()->route('home')->with('error', 'Chữ ký VNPay không hợp lệ!');
        }
    }
}
