<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quản lý Tồn Kho (Inventory)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Lập phiếu nhập hàng & Cập nhật giá</h3>
                    <form action="{{ route('admin.inventory.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                        @csrf
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mặt hàng cần nhập <span class="text-red-500">*</span></label>
                            <select name="product_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">-- Chọn Sản Phẩm --</option>
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng <span class="text-red-500">*</span></label>
                            <input type="number" name="quantity" min="1" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="VD: 50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giá gốc/nhập <span class="text-red-500">*</span></label>
                            <input type="number" name="import_price" min="0" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="VD: 15000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giá bán mới <span class="text-red-500">*</span></label>
                            <input type="number" name="selling_price" min="0" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="VD: 25000">
                        </div>
                        <div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
                                + Nhập Hàng
                            </button>
                        </div>
                        <div class="md:col-span-6 mt-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú nguồn hàng</label>
                            <input type="text" name="note" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Ví dụ: Lô hàng nhà cung cấp A ngày 15/05">
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @php
                        $lowStockProducts = $products->filter(function($p) { return $p->stock <= $p->min_stock; });
                        $normalStockProducts = $products->filter(function($p) { return $p->stock > $p->min_stock; });
                    @endphp
                    
                    <!-- Bảng Cần Nhập Khẩn Cấp -->
                    @if($lowStockProducts->count() > 0)
                        <div class="mb-10">
                            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 animate-pulse">
                                        <svg class="h-6 w-6 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-md font-bold text-red-800 uppercase tracking-wide">
                                            🚨 SẢN PHẨM SẮP HẾT CẦN NHẬP KHẨN CẤP ({{ $lowStockProducts->count() }})
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="overflow-hidden border border-red-200 rounded-lg shadow-sm">
                                <table class="w-full text-left border-collapse bg-white">
                                    <thead>
                                        <tr class="bg-red-50 border-b border-red-100">
                                            <th class="py-3 px-4 font-bold text-xs text-red-800 uppercase">Sản phẩm</th>
                                            <th class="py-3 px-4 font-bold text-xs text-red-800 uppercase text-center">Tồn kho</th>
                                            <th class="py-3 px-4 font-bold text-xs text-red-800 uppercase text-center">Ngưỡng an toàn</th>
                                            <th class="py-3 px-4 font-bold text-xs text-red-800 uppercase text-right">Giá Gốc</th>
                                            <th class="py-3 px-4 font-bold text-xs text-red-800 uppercase text-right">Giá Bán</th>
                                            <th class="py-3 px-4 font-bold text-xs text-red-800 uppercase text-center">Biên Lợi Nhuận</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-red-100">
                                        @foreach($lowStockProducts as $product)
                                            @php
                                                $profitMargin = $product->cost_price > 0 
                                                    ? (($product->price - $product->cost_price) / $product->cost_price) * 100 
                                                    : 100;
                                            @endphp
                                            <tr class="hover:bg-red-50/50 transition">
                                                <td class="py-3 px-4 flex items-center">
                                                    @if($product->image)
                                                        <img src="{{ Str::startsWith($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}" class="w-10 h-10 rounded object-cover mr-3 border border-red-200">
                                                    @else
                                                        <div class="w-10 h-10 bg-red-100 rounded mr-3 border border-red-200"></div>
                                                    @endif
                                                    <span class="font-bold text-red-900">{{ $product->name }}</span>
                                                </td>
                                                <td class="py-3 px-4 text-center font-black text-xl text-red-600">
                                                    {{ $product->stock }}
                                                </td>
                                                <td class="py-3 px-4 text-center text-red-500 font-bold">
                                                    {{ $product->min_stock }}
                                                </td>
                                                <td class="py-3 px-4 text-right text-gray-600 font-medium">
                                                    {{ number_format($product->cost_price) }}đ
                                                </td>
                                                <td class="py-3 px-4 text-right text-gray-900 font-bold">
                                                    {{ number_format($product->price) }}đ
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded font-bold text-xs">
                                                        {{ number_format($profitMargin, 1) }}%
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Bảng Tất Cả Sản Phẩm -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-4 uppercase flex items-center">
                            <span class="mr-2">📦</span> TẤT CẢ SẢN PHẨM (Tồn kho ổn định)
                        </h3>
                        <div class="overflow-hidden border border-gray-200 rounded-lg shadow-sm">
                            <table class="w-full text-left border-collapse bg-white">
                                <thead>
                                    <tr class="bg-gray-50 border-b">
                                        <th class="py-3 px-4 font-bold text-xs text-gray-600 uppercase">Sản phẩm</th>
                                        <th class="py-3 px-4 font-bold text-xs text-gray-600 uppercase text-center">Tồn kho hiện tại</th>
                                        <th class="py-3 px-4 font-bold text-xs text-gray-600 uppercase text-center">Tổng đã nhập</th>
                                        <th class="py-3 px-4 font-bold text-xs text-gray-600 uppercase text-right">Giá Gốc</th>
                                        <th class="py-3 px-4 font-bold text-xs text-gray-600 uppercase text-right">Giá Bán</th>
                                        <th class="py-3 px-4 font-bold text-xs text-gray-600 uppercase text-center">Biên Lợi Nhuận</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($normalStockProducts as $product)
                                        @php
                                            $profitMargin = $product->cost_price > 0 
                                                ? (($product->price - $product->cost_price) / $product->cost_price) * 100 
                                                : 100;
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="py-3 px-4 flex items-center">
                                                @if($product->image)
                                                    <img src="{{ Str::startsWith($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}" class="w-10 h-10 rounded object-cover mr-3 pr-0 border border-gray-200">
                                                @else
                                                    <div class="w-10 h-10 bg-gray-200 rounded mr-3"></div>
                                                @endif
                                                <span class="font-medium text-gray-900">{{ $product->name }}</span>
                                            </td>
                                            <td class="py-3 px-4 text-center font-bold text-lg text-green-600">
                                                {{ $product->stock }}
                                            </td>
                                            <td class="py-3 px-4 text-center text-gray-500 font-medium">
                                                {{ $product->product_imports_sum_quantity ?? 0 }}
                                            </td>
                                            <td class="py-3 px-4 text-right text-gray-500">
                                                {{ number_format($product->cost_price) }}đ
                                            </td>
                                            <td class="py-3 px-4 text-right font-bold text-gray-900">
                                                {{ number_format($product->price) }}đ
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded font-bold text-xs">
                                                    {{ number_format($profitMargin, 1) }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
