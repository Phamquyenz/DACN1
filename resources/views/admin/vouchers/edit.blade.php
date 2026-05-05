<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sửa Mã Giảm Giá') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.vouchers.update', $voucher) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Mã Khuyến Mãi</label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline uppercase" type="text" name="code" value="{{ old('code', $voucher->code) }}" required>
                            @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4" x-data="{ type: '{{ old('type', $voucher->type) }}' }">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Loại mã giảm giá</label>
                            <div class="flex space-x-6 mb-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="type" value="percent" x-model="type" class="mr-2 text-blue-500 focus:ring-blue-400">
                                    <span>Giảm theo %</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="type" value="amount" x-model="type" class="mr-2 text-emerald-500 focus:ring-emerald-400">
                                    <span>Giảm tiền mặt</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="type" value="freeship" x-model="type" class="mr-2 text-amber-500 focus:ring-amber-400">
                                    <span>Miễn phí giao hàng</span>
                                </label>
                            </div>

                            <div class="flex flex-wrap -mx-2">
                                <div class="w-full md:w-1/2 px-2 mb-4 md:mb-0">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Giá trị giảm <span x-text="type == 'percent' ? '(%)' : '(VNĐ)'"></span></label>
                                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="number" name="discount_value" value="{{ old('discount_value', round($voucher->discount_value)) }}" required min="0">
                                </div>
                                <div class="w-full md:w-1/2 px-2" x-show="type != 'amount'">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Giảm tối đa (VNĐ)</label>
                                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="number" name="max_discount" value="{{ old('max_discount', round($voucher->max_discount)) }}" min="0" placeholder="Bỏ trống = Ko giới hạn">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 bg-gray-50 p-4 rounded border">
                            <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Điều kiện & Tặng thưởng</h3>
                            <div class="flex flex-wrap -mx-2 mb-4">
                                <div class="w-full md:w-1/2 px-2 mb-4 md:mb-0">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Đơn hàng tối thiểu (VNĐ)</label>
                                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="number" name="min_order_value" value="{{ old('min_order_value', round($voucher->min_order_value)) }}" required min="0">
                                </div>
                                <div class="w-full md:w-1/2 px-2">
                                    <label class="block text-gray-700 text-sm font-bold mb-2 text-blue-600">Tự động tặng khi mua đạt (VNĐ) - Tùy chọn</label>
                                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline border-blue-300" type="number" name="reward_condition_amount" value="{{ old('reward_condition_amount', round($voucher->reward_condition_amount)) }}" min="0" placeholder="VD: 500000 -> Mua 500k tự tặng mã này">
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap -mx-2">
                                <div class="w-full md:w-1/2 px-2 flex items-center">
                                    <label class="flex items-center text-gray-700 text-sm font-bold cursor-pointer">
                                        <input type="checkbox" name="is_for_new_user" value="1" class="transform scale-150 mr-3 text-blue-500" {{ old('is_for_new_user', $voucher->is_for_new_user) ? 'checked' : '' }}>
                                        <span class="text-blue-600 bg-blue-50 px-2 py-1 rounded">Dành riêng KHÁCH MỚI (Tặng khi tạo TK)</span>
                                    </label>
                                </div>
                                <div class="w-full md:w-1/2 px-2 flex items-center mt-4 md:mt-0">
                                    <label class="flex items-center text-gray-700 text-sm font-bold cursor-pointer">
                                        <input type="checkbox" name="is_public" value="1" class="transform scale-150 mr-3 text-green-500" {{ old('is_public', $voucher->is_public) ? 'checked' : '' }}>
                                        <span class="text-green-600 bg-green-50 px-2 py-1 rounded">Hiển thị trong "Kho Voucher" cho mọi người lưu</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 flex flex-wrap -mx-2">
                            <div class="w-full md:w-1/2 px-2 mb-4 md:mb-0">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Giới hạn số lượng lưu/dùng</label>
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="number" name="usage_limit" value="{{ old('usage_limit', $voucher->usage_limit) }}" min="1">
                            </div>
                            <div class="w-full md:w-1/2 px-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Ngày hết hạn</label>
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="date" name="expires_at" value="{{ old('expires_at', $voucher->expires_at ? \Carbon\Carbon::parse($voucher->expires_at)->format('Y-m-d') : '') }}">
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                                Cập nhật
                            </button>
                            <a class="inline-block align-baseline font-bold text-sm text-gray-500 hover:text-gray-800" href="{{ route('admin.vouchers.index') }}">
                                Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
