<!DOCTYPE html>
<html lang="vi" class="h-full bg-beige-50 text-earth-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sơ Đồ Trang - The Stationery Muse</title>
    
    <!-- Google Fonts: Inter & Playfair Display (Serif) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,900;1,400;1,600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'Georgia', 'serif'],
                    },
                    colors: {
                        beige: {
                            50: '#fdfcfb',
                            100: '#f9f6f2',
                            200: '#f0eade',
                            300: '#e8decb',
                            400: '#d9c9ae',
                            500: '#cbb692',
                        },
                        earth: {
                            50: '#f5f2f0',
                            100: '#ece5e1',
                            200: '#d0c2ba',
                            300: '#b49f93',
                            400: '#987c6c',
                            500: '#7c5945',
                            900: '#3d2517',
                        },
                        sepia: {
                            50: '#fcf8f2',
                            100: '#f6ebd9',
                            400: '#a87c53',
                            500: '#704214',
                            600: '#5a3510',
                            700: '#46290c',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fdfcfb;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(203, 182, 146, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(112, 66, 20, 0.05) 0%, transparent 40%);
            background-attachment: fixed;
        }
        .vintage-card {
            background: #ffffff;
            border: 1px solid rgba(240, 234, 222, 0.8);
            box-shadow: 0 15px 40px -15px rgba(61, 37, 23, 0.05);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .vintage-card:hover {
            border-color: rgba(112, 66, 20, 0.2);
            box-shadow: 0 25px 50px -20px rgba(61, 37, 23, 0.08);
            transform: translateY(-4px);
        }
        .vintage-inner-card {
            background: #f9f6f2;
            border: 1px solid rgba(240, 234, 222, 0.5);
            transition: all 0.3s ease;
        }
        .vintage-inner-card:hover {
            background: #ffffff;
            border-color: rgba(112, 66, 20, 0.3);
            box-shadow: 0 10px 25px -10px rgba(112, 66, 20, 0.08);
        }
    </style>
</head>
<body class="min-h-full py-16 px-4 sm:px-6 lg:px-8 flex flex-col justify-between">

    <!-- Main Container -->
    <div class="max-w-6xl mx-auto w-full flex-1">
        
        <!-- Header -->
        <header class="flex flex-col md:flex-row md:justify-between md:items-end gap-6 mb-16 pb-8 border-b border-beige-200">
            <div class="space-y-4 max-w-2xl">
                <div class="inline-flex items-center space-x-2">
                    <span class="h-px w-6 bg-sepia-400"></span>
                    <span class="text-xs font-bold uppercase tracking-[0.4em] text-sepia-500">
                        ĐỊNH VỊ URL • SYNTH MAP
                    </span>
                    <span class="h-px w-6 bg-sepia-400"></span>
                </div>
                <h1 class="text-5xl md:text-6xl font-serif text-earth-900 tracking-tight leading-none font-bold">
                    Sơ Đồ Trang
                </h1>
                <p class="text-sm md:text-base text-earth-500/80 font-medium leading-relaxed">
                    Liệt kê các đường dẫn chính của hệ thống. Trang này dùng giao diện độc lập – không chia sẻ menu hay footer với cửa hàng hay quản trị.
                </p>
            </div>
            
            <!-- Actions -->
            <div class="flex items-center gap-4">
                <a href="{{ url('/') }}" class="px-6 py-3.5 rounded-2xl border border-beige-300 text-earth-800 text-xs font-bold uppercase tracking-[0.2em] hover:bg-earth-900 hover:text-white hover:border-earth-900 transition duration-500 flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Về Trang Chủ
                </a>
                <a href="{{ route('sitemap.xml') }}" target="_blank" class="px-7 py-3.5 rounded-2xl bg-sepia-500 text-white text-xs font-bold uppercase tracking-[0.2em] hover:bg-sepia-600 shadow-xl shadow-earth-900/10 transition duration-500 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path>
                    </svg>
                    Sitemap.xml
                </a>
            </div>
        </header>

        <!-- Status update time -->
        <div class="mb-10 flex items-center gap-3 text-[10px] font-bold text-earth-400 uppercase tracking-[0.25em]">
            <span class="w-2 h-2 bg-sepia-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(112,66,20,0.5)]"></span>
            CẬP NHẬT: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} - UTC
        </div>

        <div class="space-y-12">
            
            <!-- SECTION 1: KHU VỰC CÔNG KHAI -->
            <section class="vintage-card rounded-[2.5rem] p-8 md:p-10">
                <div class="flex items-center gap-4 mb-8 border-b border-beige-100 pb-4">
                    <div class="p-3 bg-sepia-50 rounded-2xl text-sepia-500 border border-beige-200/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-serif font-bold text-earth-900 tracking-tight">Khu Vực Công Khai</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Home -->
                    <a href="{{ url('/') }}" class="vintage-inner-card p-6 rounded-2xl group flex flex-col justify-between">
                        <div class="text-earth-900 font-bold text-sm mb-1 group-hover:text-sepia-500 transition duration-300">Trang chủ</div>
                        <div class="text-xs text-earth-400 font-mono">{{ url('/') }}</div>
                    </a>
                    
                    <!-- Products -->
                    <a href="{{ route('products.index') }}" class="vintage-inner-card p-6 rounded-2xl group flex flex-col justify-between">
                        <div class="text-earth-900 font-bold text-sm mb-1 group-hover:text-sepia-500 transition duration-300">Danh mục sản phẩm</div>
                        <div class="text-xs text-earth-400 font-mono">{{ route('products.index') }}</div>
                    </a>
                    
                    <!-- Flash Sale -->
                    <a href="{{ route('flash_sale') }}" class="vintage-inner-card p-6 rounded-2xl group flex flex-col justify-between">
                        <div class="text-earth-900 font-bold text-sm mb-1 group-hover:text-sepia-500 transition duration-300">Flash Sale</div>
                        <div class="text-xs text-earth-400 font-mono">{{ route('flash_sale') }}</div>
                    </a>
                    
                    <!-- Cart -->
                    <a href="{{ route('cart.index') }}" class="vintage-inner-card p-6 rounded-2xl group flex flex-col justify-between">
                        <div class="text-earth-900 font-bold text-sm mb-1 group-hover:text-sepia-500 transition duration-300">Giỏ hàng</div>
                        <div class="text-xs text-earth-400 font-mono">{{ route('cart.index') }}</div>
                    </a>
                    
                    <!-- Login -->
                    <a href="{{ route('login') }}" class="vintage-inner-card p-6 rounded-2xl group flex flex-col justify-between">
                        <div class="text-earth-900 font-bold text-sm mb-1 group-hover:text-sepia-500 transition duration-300">Đăng nhập</div>
                        <div class="text-xs text-earth-400 font-mono">{{ route('login') }}</div>
                    </a>
                    
                    <!-- Register -->
                    <a href="{{ route('register') }}" class="vintage-inner-card p-6 rounded-2xl group flex flex-col justify-between">
                        <div class="text-earth-900 font-bold text-sm mb-1 group-hover:text-sepia-500 transition duration-300">Đăng ký</div>
                        <div class="text-xs text-earth-400 font-mono">{{ route('register') }}</div>
                    </a>

                    <!-- Newsletter (Form trang chu) -->
                    <div class="vintage-inner-card p-6 rounded-2xl flex flex-col justify-between opacity-70">
                        <div class="text-earth-900 font-bold text-sm mb-1">Đăng ký nhận tin</div>
                        <div class="text-xs text-earth-400 font-mono">POST (form trang chủ)</div>
                    </div>

                    <!-- Sitemap (This page) -->
                    <a href="{{ route('sitemap') }}" class="vintage-inner-card p-6 rounded-2xl border border-sepia-500/20 bg-sepia-50/50 group flex flex-col justify-between">
                        <div class="text-sepia-600 font-bold text-sm mb-1 group-hover:text-sepia-700 transition duration-300 flex items-center gap-2">
                            <span>Sơ đồ trang (trang này)</span>
                            <span class="w-2 h-2 bg-sepia-500 rounded-full"></span>
                        </div>
                        <div class="text-xs text-sepia-500 font-mono">{{ route('sitemap') }}</div>
                    </a>
                </div>
            </section>

            <!-- SECTION 2: CHI TIẾT SẢN PHẨM -->
            <section class="vintage-card rounded-[2.5rem] p-8 md:p-10">
                <div class="flex items-center justify-between gap-4 mb-8 border-b border-beige-100 pb-4">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-sepia-50 rounded-2xl text-sepia-500 border border-beige-200/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-serif font-bold text-earth-900 tracking-tight">Chi Tiết Sản Phẩm</h2>
                    </div>
                    <span class="px-4 py-1.5 bg-sepia-100 text-sepia-700 border border-sepia-200 rounded-full text-xs font-bold tracking-wider">
                        {{ $products->count() }} SKU
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($products as $product)
                        <a href="{{ route('product.show', $product->id) }}" class="vintage-inner-card p-6 rounded-2xl group flex flex-col justify-between h-40">
                            <div>
                                <span class="px-2.5 py-1 bg-beige-200 text-earth-500 rounded-lg text-[9px] font-bold uppercase tracking-wider">
                                    SKU #{{ $product->id }}
                                </span>
                                <div class="text-earth-900 font-serif font-bold text-sm mt-3 line-clamp-1 group-hover:text-sepia-500 transition duration-300">
                                    {{ $product->name }}
                                </div>
                            </div>
                            <div class="flex justify-between items-end border-t border-beige-200/60 pt-3 mt-3">
                                <span class="text-sm font-bold text-sepia-600 font-mono">{{ number_format($product->price) }}đ</span>
                                <span class="text-[10px] text-earth-400 font-medium">Tồn kho: <span class="text-earth-700 font-bold">{{ $product->stock }}</span></span>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full p-12 text-center text-earth-400 italic text-sm">
                            Chưa có sản phẩm nào trong hệ thống.
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- SECTION 3: KHU VỰC THÀNH VIÊN (MEMBER ZONE) -->
            <section class="vintage-card rounded-[2.5rem] p-8 md:p-10">
                <div class="flex items-center justify-between mb-8 border-b border-beige-100 pb-4">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-sepia-50 rounded-2xl text-sepia-500 border border-beige-200/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-serif font-bold text-earth-900 tracking-tight">Khu Vực Thành Viên</h2>
                    </div>
                    @auth
                        <span class="px-4 py-1.5 bg-green-50 text-green-700 border border-green-200 rounded-full text-xs font-bold">
                            Chào, {{ auth()->user()->name }}
                        </span>
                    @else
                        <span class="px-4 py-1.5 bg-earth-100 text-earth-600 border border-beige-200 rounded-full text-xs font-bold">
                            Khách vãng lai
                        </span>
                    @endauth
                </div>

                @auth
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('profile.edit') }}" class="vintage-inner-card p-6 rounded-2xl group flex flex-col justify-between">
                            <div class="text-earth-900 font-bold text-sm mb-1 group-hover:text-sepia-500 transition duration-300">Hồ sơ cá nhân</div>
                            <div class="text-xs text-earth-400 font-mono">{{ route('profile.edit') }}</div>
                        </a>
                        <a href="{{ route('orders.index') }}" class="vintage-inner-card p-6 rounded-2xl group flex flex-col justify-between">
                            <div class="text-earth-900 font-bold text-sm mb-1 group-hover:text-sepia-500 transition duration-300">Đơn hàng của tôi</div>
                            <div class="text-xs text-earth-400 font-mono">{{ route('orders.index') }}</div>
                        </a>
                        <a href="{{ route('my-vouchers.index') }}" class="vintage-inner-card p-6 rounded-2xl group flex flex-col justify-between">
                            <div class="text-earth-900 font-bold text-sm mb-1 group-hover:text-sepia-500 transition duration-300">Ví Voucher</div>
                            <div class="text-xs text-earth-400 font-mono">{{ route('my-vouchers.index') }}</div>
                        </a>
                    </div>
                @else
                    <div class="p-10 text-center vintage-inner-card rounded-2xl border border-dashed border-beige-300">
                        <p class="text-earth-500 font-medium text-sm mb-6">Bạn chưa đăng nhập. Vui lòng đăng nhập để sử dụng các tính năng dành cho thành viên.</p>
                        <a href="{{ route('login') }}" class="inline-flex px-8 py-3 bg-sepia-500 text-white font-bold text-xs uppercase tracking-[0.2em] rounded-xl shadow-lg shadow-earth-900/5 hover:bg-sepia-600 transition">
                            Đăng Nhập Ngay
                        </a>
                    </div>
                @endauth
            </section>

            <!-- SECTION 4: KHU VỰC QUẢN TRỊ (ADMIN ZONE) -->
            @if(auth()->check() && auth()->user()->role === 'admin')
                <section class="vintage-card rounded-[2.5rem] p-8 md:p-10 border border-red-200 bg-red-50/10">
                    <div class="flex items-center gap-4 mb-8 border-b border-red-100 pb-4">
                        <div class="p-3 bg-red-50 rounded-2xl text-red-500 border border-red-100/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-serif font-bold text-red-900 tracking-tight">Khu Vực Quản Trị</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="{{ route('admin.dashboard') }}" class="vintage-inner-card p-6 rounded-2xl group flex flex-col justify-between border-l-4 border-l-red-500">
                            <div class="text-earth-900 font-bold text-sm mb-1 group-hover:text-red-500 transition duration-300">Bảng điều khiển quản trị</div>
                            <div class="text-xs text-earth-400 font-mono">{{ route('admin.dashboard') }}</div>
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="vintage-inner-card p-6 rounded-2xl group flex flex-col justify-between">
                            <div class="text-earth-900 font-bold text-sm mb-1 group-hover:text-red-500 transition duration-300">Quản lý Danh mục</div>
                            <div class="text-xs text-earth-400 font-mono">{{ route('admin.categories.index') }}</div>
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="vintage-inner-card p-6 rounded-2xl group flex flex-col justify-between">
                            <div class="text-earth-900 font-bold text-sm mb-1 group-hover:text-red-500 transition duration-300">Quản lý Sản phẩm</div>
                            <div class="text-xs text-earth-400 font-mono">{{ route('admin.products.index') }}</div>
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="vintage-inner-card p-6 rounded-2xl group flex flex-col justify-between">
                            <div class="text-earth-900 font-bold text-sm mb-1 group-hover:text-red-500 transition duration-300">Quản lý Đơn hàng</div>
                            <div class="text-xs text-earth-400 font-mono">{{ route('admin.orders.index') }}</div>
                        </a>
                        <a href="{{ route('admin.vouchers.index') }}" class="vintage-inner-card p-6 rounded-2xl group flex flex-col justify-between">
                            <div class="text-earth-900 font-bold text-sm mb-1 group-hover:text-red-500 transition duration-300">Quản lý Vouchers</div>
                            <div class="text-xs text-earth-400 font-mono">{{ route('admin.vouchers.index') }}</div>
                        </a>
                        <a href="{{ route('admin.inventory.index') }}" class="vintage-inner-card p-6 rounded-2xl group flex flex-col justify-between">
                            <div class="text-earth-900 font-bold text-sm mb-1 group-hover:text-red-500 transition duration-300">Quản lý Hàng tồn kho</div>
                            <div class="text-xs text-earth-400 font-mono">{{ route('admin.inventory.index') }}</div>
                        </a>
                        <a href="{{ route('admin.contacts.index') }}" class="vintage-inner-card p-6 rounded-2xl group flex flex-col justify-between">
                            <div class="text-earth-900 font-bold text-sm mb-1 group-hover:text-red-500 transition duration-300">Quản lý Liên lạc & Chat</div>
                            <div class="text-xs text-earth-400 font-mono">{{ route('admin.contacts.index') }}</div>
                        </a>
                    </div>
                </section>
            @endif

        </div>

    </div>

    <!-- Footer -->
    <footer class="mt-24 py-8 border-t border-beige-200 text-center">
        <p class="text-xs text-earth-400 font-medium tracking-[0.2em] uppercase">
            &copy; {{ date('Y') }} THE STATIONERY MUSE. TẤT CẢ CÁC QUYỀN ĐƯỢC BẢO LƯU.
        </p>
    </footer>

</body>
</html>
