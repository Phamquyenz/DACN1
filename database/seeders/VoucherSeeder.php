<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Voucher;
use App\Models\User;
use Carbon\Carbon;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Xóa vouchers cũ để tạo lại cho sạch
        Schema::disableForeignKeyConstraints();
        DB::table('user_voucher')->truncate();
        DB::table('vouchers')->truncate();
        Schema::enableForeignKeyConstraints();

        // 5 Mã dành cho tân thủ (Tặng tự động)
        $newVouchers = [
            [
                'code' => 'FREESHIP',
                'type' => 'freeship',
                'discount_value' => 30000,
                'max_discount' => 30000,
                'min_order_value' => 0,
                'usage_limit' => 1000,
                'is_for_new_user' => true,
                'expires_at' => Carbon::now()->addMonths(1),
            ],
            [
                'code' => 'Discount_10',
                'type' => 'percent',
                'discount_value' => 10,
                'max_discount' => 20000,
                'min_order_value' => 100000,
                'usage_limit' => 1000,
                'is_for_new_user' => true,
                'expires_at' => Carbon::now()->addMonths(1),
            ],
            [
                'code' => 'NEWBIE_20',
                'type' => 'percent',
                'discount_value' => 20,
                'max_discount' => 50000,
                'min_order_value' => 250000,
                'usage_limit' => 1000,
                'is_for_new_user' => true,
                'expires_at' => Carbon::now()->addMonths(1),
            ],
            [
                'code' => 'WELCOME_GIFT_50K',
                'type' => 'amount',
                'discount_value' => 50000,
                'max_discount' => 50000,
                'min_order_value' => 500000,
                'usage_limit' => 1000,
                'is_for_new_user' => true,
                'expires_at' => Carbon::now()->addMonths(1),
            ],
            [
                'code' => 'FIRST_ORDER_BONUS',
                'type' => 'amount',
                'discount_value' => 100000,
                'max_discount' => 100000,
                'min_order_value' => 1000000,
                'usage_limit' => 1000,
                'is_for_new_user' => true,
                'expires_at' => Carbon::now()->addMonths(1),
            ]
        ];

        // 5 Mã dành cho mọi người
        $publicVouchers = [
            [
                'code' => 'MUSE_FREESHIP',
                'type' => 'freeship',
                'discount_value' => 30000,
                'max_discount' => 30000,
                'min_order_value' => 150000,
                'usage_limit' => 500,
                'is_for_new_user' => false,
                'expires_at' => Carbon::now()->addMonths(3),
            ],
            [
                'code' => 'SPRING_SALE_15',
                'type' => 'percent',
                'discount_value' => 15,
                'max_discount' => 40000,
                'min_order_value' => 300000,
                'usage_limit' => 500,
                'is_for_new_user' => false,
                'expires_at' => Carbon::now()->addMonths(3),
            ],
            [
                'code' => 'VIP_MUSE_25',
                'type' => 'percent',
                'discount_value' => 25,
                'max_discount' => 100000,
                'min_order_value' => 800000,
                'usage_limit' => 100,
                'is_for_new_user' => false,
                'expires_at' => Carbon::now()->addMonths(3),
            ],
            [
                'code' => 'BIG_BONUS_200K',
                'type' => 'amount',
                'discount_value' => 200000,
                'max_discount' => 200000,
                'min_order_value' => 2000000,
                'usage_limit' => 50,
                'is_for_new_user' => false,
                'expires_at' => Carbon::now()->addMonths(3),
            ],
            [
                'code' => 'WEEKEND_DEAL',
                'type' => 'percent',
                'discount_value' => 10,
                'max_discount' => 150000,
                'min_order_value' => 500000,
                'usage_limit' => 200,
                'is_for_new_user' => false,
                'expires_at' => Carbon::now()->addDays(2),
            ],
        ];

        $allVouchersData = array_merge($newVouchers, $publicVouchers);
        $insertedVouchers = [];

        foreach ($allVouchersData as $vData) {
            $insertedVouchers[] = Voucher::create($vData);
        }

        // Tặng 5 mã Tân thủ cho TẤT CẢ khách hàng hiện tại
        $users = User::where('role', 'customer')->orWhereNull('role')->get(); // giả sử có cột role, nếu không có thì lấy all
        $newbieVouchers = array_filter($insertedVouchers, function($v) {
            return $v->is_for_new_user;
        });

        foreach ($users as $user) {
            foreach ($newbieVouchers as $v) {
                // Attach without detaching
                DB::table('user_voucher')->insertOrIgnore([
                    'user_id' => $user->id,
                    'voucher_id' => $v->id,
                    'is_used' => false,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
