<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherWalletController extends Controller
{
    // Hiển thị Kho Voucher của tôi
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Lấy các voucher người dùng đã lưu
        $savedVouchers = $user->vouchers()
            ->orderBy('user_voucher.created_at', 'desc')
            ->get();
            
        // Lấy các voucher public mà người dùng chưa lưu
        $savedVoucherIds = $savedVouchers->pluck('id')->toArray();
        $publicVouchers = Voucher::where('is_public', true)
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', \Carbon\Carbon::now());
            })
            ->whereNotIn('id', $savedVoucherIds)
            ->get();

        return view('vouchers.my_vouchers', compact('savedVouchers', 'publicVouchers'));
    }

    // Lưu mã public vào ví
    public function save(Request $request)
    {
        $request->validate(['voucher_id' => 'required|exists:vouchers,id']);
        
        $user = Auth::user();
        $voucher = Voucher::findOrFail($request->voucher_id);
        
        if (!$voucher->is_public) {
            return response()->json(['success' => false, 'message' => 'Voucher này không được phép lưu công khai.']);
        }
        
        $alreadyHas = \Illuminate\Support\Facades\DB::table('user_voucher')
            ->where('user_id', $user->id)
            ->where('voucher_id', $voucher->id)
            ->exists();

        if ($alreadyHas) {
            return response()->json(['success' => false, 'message' => 'Bạn đã lưu mã này rồi.']);
        }
        
        \Illuminate\Support\Facades\DB::table('user_voucher')->insert([
            'user_id' => $user->id,
            'voucher_id' => $voucher->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã lưu mã thành công!']);
        }
        
        return back()->with('success', 'Đã lưu mã giảm giá vào Kho Voucher của bạn.');
    }
}
