<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->decimal('reward_condition_amount', 15, 2)->nullable()->after('min_order_value')->comment('Tự động tặng mã này nếu hóa đơn đạt mức này');
            $table->boolean('is_public')->default(true)->after('is_for_new_user')->comment('Cho phép hiện công khai trong Kho Voucher');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['reward_condition_amount', 'is_public']);
        });
    }
};
