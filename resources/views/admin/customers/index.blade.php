<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quản lý Khách Hàng') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex space-x-4">
                <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 rounded-lg font-bold text-sm {{ $filter == 'all' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border' }}">Tất cả</a>
                <a href="{{ route('admin.customers.index', ['filter' => 'vip']) }}" class="px-4 py-2 rounded-lg font-bold text-sm {{ $filter == 'vip' ? 'bg-yellow-500 text-white' : 'bg-white text-yellow-600 border border-yellow-500' }}">👑 Khách VIP</a>
                <a href="{{ route('admin.customers.index', ['filter' => 'new']) }}" class="px-4 py-2 rounded-lg font-bold text-sm {{ $filter == 'new' ? 'bg-green-500 text-white' : 'bg-white text-green-600 border border-green-500' }}">🌱 Khách mới</a>
                <a href="{{ route('admin.customers.index', ['filter' => 'potential']) }}" class="px-4 py-2 rounded-lg font-bold text-sm {{ $filter == 'potential' ? 'bg-blue-500 text-white' : 'bg-white text-blue-600 border border-blue-500' }}">💎 Khách tiềm năng</a>
                <a href="{{ route('admin.customers.index', ['filter' => 'inactive']) }}" class="px-4 py-2 rounded-lg font-bold text-sm {{ $filter == 'inactive' ? 'bg-red-500 text-white' : 'bg-white text-red-600 border border-red-500' }}">💤 Lâu chưa mua</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b border-gray-200">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b">
                                <th class="py-3 px-4 font-bold text-sm text-gray-600 uppercase">Khách hàng</th>
                                <th class="py-3 px-4 font-bold text-sm text-gray-600 uppercase">Phân loại</th>
                                <th class="py-3 px-4 font-bold text-sm text-gray-600 uppercase text-center">Số đơn</th>
                                <th class="py-3 px-4 font-bold text-sm text-gray-600 uppercase text-right">Tổng chi tiêu</th>
                                <th class="py-3 px-4 font-bold text-sm text-gray-600 uppercase text-right">Mua lần cuối</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($customers as $customer)
                                @php
                                    $isVip = $customer->orders_sum_total_price > 5000000 || $customer->orders_count >= 5;
                                    $isNew = $customer->created_at >= \Carbon\Carbon::now()->subDays(30);
                                    $isPotential = $customer->orders_count == 0;
                                    $lastOrder = $customer->orders->first();
                                    $isInactive = $lastOrder && $lastOrder->created_at < \Carbon\Carbon::now()->subDays(30);
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-gray-900">{{ $customer->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $customer->email }}</div>
                                        <div class="text-[10px] text-gray-400 mt-1">Đăng ký: {{ $customer->created_at->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="py-4 px-4 space-y-1">
                                        @if($isVip)
                                            <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 text-[10px] font-bold rounded-full">👑 VIP</span>
                                        @endif
                                        @if($isNew)
                                            <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-[10px] font-bold rounded-full">🌱 Mới</span>
                                        @endif
                                        @if($isPotential)
                                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-[10px] font-bold rounded-full">💎 Tiềm năng</span>
                                        @endif
                                        @if($isInactive)
                                            <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-[10px] font-bold rounded-full">💤 Lâu chưa mua</span>
                                        @endif
                                        @if(!$isVip && !$isNew && !$isPotential && !$isInactive)
                                            <span class="inline-block px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold rounded-full">Thành viên</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center font-bold text-gray-700">
                                        {{ $customer->orders_count }}
                                    </td>
                                    <td class="py-4 px-4 text-right font-bold text-indigo-600">
                                        {{ number_format($customer->orders_sum_total_price ?? 0) }}đ
                                    </td>
                                    <td class="py-4 px-4 text-right text-sm text-gray-500">
                                        {{ $lastOrder ? $lastOrder->created_at->diffForHumans() : 'Chưa từng mua' }}
                                    </td>
                                </tr>
                            @endforeach
                            @if($customers->isEmpty())
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-400">Không tìm thấy khách hàng nào phù hợp với bộ lọc.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    
                    <div class="mt-4">
                        {{ $customers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
