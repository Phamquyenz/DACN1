<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chi tiết Đơn hàng #') . $order->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Thông tin khách hàng & Đơn hàng -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg col-span-1">
                    <div class="p-6 text-gray-900 border-b border-gray-200">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Thông tin chung</h3>
                        <p class="mb-2"><strong>Khách hàng:</strong> {{ $order->user->name }}</p>
                        <p class="mb-2"><strong>Email:</strong> {{ $order->user->email }}</p>
                        <p class="mb-2"><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        <p class="mb-2"><strong>Tổng số tiền:</strong> <span class="text-red-500 font-bold">{{ number_format($order->total_price) }} đ</span></p>
                        <p class="mb-4"><strong>Trạng thái hiện tại:</strong> 
                            <span class="font-bold text-gray-700">{{ $order->status }}</span>
                        </p>

                        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="mt-4 border-t pt-4">
                            @csrf
                            @method('PUT')
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Cập nhật trạng thái</label>
                            <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm mb-4">
                                <option value="Pending" {{ $order->status == 'Pending' ? 'selected' : '' }}>Chờ xử lý (Pending)</option>
                                <option value="Shipping" {{ $order->status == 'Shipping' ? 'selected' : '' }}>Đang giao (Shipping)</option>
                                <option value="Completed" {{ $order->status == 'Completed' ? 'selected' : '' }}>Hoàn thành (Completed)</option>
                                <option value="Canceled" {{ $order->status == 'Canceled' ? 'selected' : '' }}>Đã hủy (Canceled)</option>
                            </select>
                            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Cập nhật</button>
                        </form>
                        
                        @if(session('success'))
                            <div class="mt-4 bg-green-100 text-green-700 p-2 rounded text-sm">
                                {{ session('success') }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Danh sách sản phẩm -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg col-span-2">
                    <div class="p-6 text-gray-900 border-b border-gray-200">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Danh sách Sản phẩm</h3>
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th class="border-b py-2 px-4 uppercase text-sm font-bold text-gray-600">Sản phẩm</th>
                                    <th class="border-b py-2 px-4 uppercase text-sm font-bold text-gray-600 text-center">Đơn giá</th>
                                    <th class="border-b py-2 px-4 uppercase text-sm font-bold text-gray-600 text-center">Số lượng</th>
                                    <th class="border-b py-2 px-4 uppercase text-sm font-bold text-gray-600 text-right">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td class="border-b py-3 px-4 flex items-center">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ Str::startsWith($item->product->image, 'http') ? $item->product->image : asset('storage/' . $item->product->image) }}" class="w-12 h-12 rounded object-cover mr-4">
                                            @else
                                                <div class="w-12 h-12 bg-gray-200 rounded mr-4"></div>
                                            @endif
                                            <span>{{ $item->product ? $item->product->name : 'Sản phẩm không còn tồn tại' }}</span>
                                        </td>
                                        <td class="border-b py-3 px-4 text-center">{{ number_format($item->price) }} đ</td>
                                        <td class="border-b py-3 px-4 text-center">{{ $item->quantity }}</td>
                                        <td class="border-b py-3 px-4 text-right font-semibold">{{ number_format($item->price * $item->quantity) }} đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
