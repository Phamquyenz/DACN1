<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Kho Voucher của tôi') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">🎟️ Mã Khuyến Mãi Của Bạn</h3>
                
                @if($savedVouchers->isEmpty())
                    <div class="bg-white p-8 rounded-xl shadow-sm text-center">
                        <div class="text-6xl mb-4">🎫</div>
                        <h4 class="text-lg font-bold text-gray-600">Kho Voucher trống</h4>
                        <p class="text-gray-500 mt-2">Bạn chưa lưu hoặc nhận được mã giảm giá nào. Khám phá các mã bên dưới để lưu nhé!</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($savedVouchers as $voucher)
                            @php
                                $isUsed = $voucher->pivot->is_used;
                                $isExpired = $voucher->expires_at && \Carbon\Carbon::parse($voucher->expires_at)->isPast();
                                $disabled = $isUsed || $isExpired;
                            @endphp
                            <div class="bg-white rounded-xl shadow-md overflow-hidden border {{ $disabled ? 'border-gray-200 opacity-60' : 'border-indigo-100' }} flex flex-col relative">
                                @if($isUsed)
                                    <div class="absolute top-0 right-0 bg-gray-600 text-white text-[10px] font-bold px-3 py-1 rounded-bl-lg z-10">Đã dùng</div>
                                @elseif($isExpired)
                                    <div class="absolute top-0 right-0 bg-red-600 text-white text-[10px] font-bold px-3 py-1 rounded-bl-lg z-10">Hết hạn</div>
                                @endif

                                <div class="p-5 flex-grow border-b border-dashed border-gray-200 relative">
                                    <div class="absolute left-[-10px] bottom-[-10px] w-5 h-5 bg-gray-50 rounded-full border border-gray-200 z-10"></div>
                                    <div class="absolute right-[-10px] bottom-[-10px] w-5 h-5 bg-gray-50 rounded-full border border-gray-200 z-10"></div>
                                    
                                    <h4 class="font-bold text-xl mb-1 
                                        {{ $voucher->type == 'percent' ? 'text-blue-600' : '' }}
                                        {{ $voucher->type == 'amount' ? 'text-emerald-600' : '' }}
                                        {{ $voucher->type == 'freeship' ? 'text-amber-500' : '' }}
                                    ">
                                        @if($voucher->type == 'percent')
                                            Giảm {{ $voucher->discount_value }}%
                                        @elseif($voucher->type == 'amount')
                                            Giảm {{ number_format($voucher->discount_value) }}đ
                                        @else
                                            Freeship {{ number_format($voucher->discount_value) }}đ
                                        @endif
                                    </h4>
                                    
                                    <p class="text-xs text-gray-500 mb-2">Đơn tối thiểu: <span class="font-bold text-gray-700">{{ number_format($voucher->min_order_value) }}đ</span></p>
                                    @if($voucher->max_discount)
                                        <p class="text-xs text-gray-500 mb-2">Giảm tối đa: <span class="font-bold text-gray-700">{{ number_format($voucher->max_discount) }}đ</span></p>
                                    @endif
                                    <div class="mt-3 bg-gray-100 text-center py-2 rounded border border-gray-200">
                                        <span class="font-mono font-bold tracking-widest text-gray-800">{{ $voucher->code }}</span>
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-3 text-center flex justify-between items-center">
                                    <span class="text-xs text-gray-500">
                                        HSD: {{ $voucher->expires_at ? \Carbon\Carbon::parse($voucher->expires_at)->format('d/m/Y') : 'Không giới hạn' }}
                                    </span>
                                    <a href="{{ route('products.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase">Dùng ngay</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Public Vouchers -->
            @if($publicVouchers->count() > 0)
                <div class="mt-12">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">🎁 Mã Có Thể Lưu</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($publicVouchers as $voucher)
                            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-emerald-100 flex flex-col relative">
                                <div class="p-5 flex-grow border-b border-dashed border-gray-200 relative">
                                    <div class="absolute left-[-10px] bottom-[-10px] w-5 h-5 bg-gray-50 rounded-full border border-gray-200 z-10"></div>
                                    <div class="absolute right-[-10px] bottom-[-10px] w-5 h-5 bg-gray-50 rounded-full border border-gray-200 z-10"></div>
                                    
                                    <h4 class="font-bold text-xl mb-1 
                                        {{ $voucher->type == 'percent' ? 'text-blue-600' : '' }}
                                        {{ $voucher->type == 'amount' ? 'text-emerald-600' : '' }}
                                        {{ $voucher->type == 'freeship' ? 'text-amber-500' : '' }}
                                    ">
                                        @if($voucher->type == 'percent')
                                            Giảm {{ $voucher->discount_value }}%
                                        @elseif($voucher->type == 'amount')
                                            Giảm {{ number_format($voucher->discount_value) }}đ
                                        @else
                                            Freeship {{ number_format($voucher->discount_value) }}đ
                                        @endif
                                    </h4>
                                    
                                    <p class="text-xs text-gray-500 mb-2">Đơn tối thiểu: <span class="font-bold text-gray-700">{{ number_format($voucher->min_order_value) }}đ</span></p>
                                    @if($voucher->max_discount)
                                        <p class="text-xs text-gray-500 mb-2">Giảm tối đa: <span class="font-bold text-gray-700">{{ number_format($voucher->max_discount) }}đ</span></p>
                                    @endif
                                    <p class="text-[10px] text-gray-400 mt-2">Mã: <span class="font-mono text-gray-600">*********</span> (Lưu để xem)</p>
                                </div>
                                <div class="bg-gray-50 p-3 flex justify-between items-center">
                                    <span class="text-[10px] text-gray-500">
                                        HSD: {{ $voucher->expires_at ? \Carbon\Carbon::parse($voucher->expires_at)->format('d/m/Y') : 'Không giới hạn' }}
                                    </span>
                                    <form action="{{ route('my-vouchers.save') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="voucher_id" value="{{ $voucher->id }}">
                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2 px-4 rounded-full shadow-lg shadow-indigo-600/30 transition transform hover:scale-105">
                                            LƯU MÃ
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
