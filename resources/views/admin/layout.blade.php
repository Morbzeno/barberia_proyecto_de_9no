<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de administración') · Machin Barber</title>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js"></script>
    <style>
        body { font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif; }
        .font-display { font-family: 'Fraunces', serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#f4f1ea] text-[#22190f] antialiased" x-data="{ open: false }">
<div class="min-h-screen flex">

    {{-- SIDEBAR --}}
    <aside class="w-64 shrink-0 bg-[#15110c] text-[#f3ead9] flex flex-col fixed inset-y-0 left-0 z-30 transition-transform"
           :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        <div class="px-6 py-6 border-b border-white/10">
            <p class="font-display text-xl tracking-wide text-[#d9a862]">Machin Barber</p>
            <p class="text-xs text-[#9c8b74] mt-1">Panel de administración</p>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 space-y-1 text-sm">
            @php
                $links = [
                    ['route' => 'admin.dashboard', 'label' => 'Resumen', 'icon' => '📊'],
                    ['route' => 'admin.appointments.index', 'label' => 'Citas', 'icon' => '📅'],
                    ['route' => 'admin.sells.index', 'label' => 'Ventas', 'icon' => '🧾'],
                    ['route' => 'admin.payments.index', 'label' => 'Pagos', 'icon' => '💳'],
                    ['route' => 'admin.products.index', 'label' => 'Productos', 'icon' => '🛍️'],
                    ['route' => 'admin.categories.index', 'label' => 'Categorías', 'icon' => '🏷️'],
                    ['route' => 'admin.services.index', 'label' => 'Servicios', 'icon' => '💈'],
                    ['route' => 'admin.chairs.index', 'label' => 'Sillas', 'icon' => '🪑'],
                    ['route' => 'admin.employees.index', 'label' => 'Empleados', 'icon' => '👤'],
                    ['route' => 'admin.clients.index', 'label' => 'Clientes', 'icon' => '🧑‍🤝‍🧑'],
                ];
            @endphp

            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}"
                   class="flex items-center gap-3 px-6 py-2.5 transition-colors border-l-2
                          {{ request()->routeIs(str_replace('.index', '', $link['route']).'*') || request()->routeIs($link['route'])
                                ? 'bg-white/10 border-[#d9a862] text-[#f3ead9]'
                                : 'border-transparent text-[#cdbfa8] hover:bg-white/5 hover:text-[#f3ead9]' }}">
                    <span>{{ $link['icon'] }}</span>
                    <span>{{ $link['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="px-6 py-4 border-t border-white/10 text-xs text-[#9c8b74] space-y-3">
            @auth
                <p class="text-[#cdbfa8]">{{ auth()->user()->email }}</p>
            @endauth
            <a href="{{ route('home') }}" class="block hover:text-[#d9a862]">← Volver al sitio</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left hover:text-[#d9a862]">Cerrar sesión</button>
            </form>
        </div>
    </aside>

    {{-- BACKDROP (mobile) --}}
    <div x-show="open" x-cloak @click="open = false" class="fixed inset-0 bg-black/40 z-20 lg:hidden"></div>

    {{-- CONTENT --}}
    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">
        <header class="bg-white border-b border-black/10 px-4 lg:px-8 py-4 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <button @click="open = !open" class="lg:hidden text-xl leading-none">☰</button>
                <div>
                <h1 class="font-display text-2xl">@yield('title', 'Resumen')</h1>
                @hasSection('subtitle')
                    <p class="text-sm text-[#6b5c46]">@yield('subtitle')</p>
                @endif
                </div>
            </div>
            @yield('header-actions')
        </header>

        <main class="flex-1 p-4 lg:p-8">
            @if (session('success'))
                <div class="mb-6 rounded-lg border border-[#5fbf83]/30 bg-[#5fbf83]/10 text-[#2f6b45] px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-lg border border-[#e2685a]/30 bg-[#e2685a]/10 text-[#a3352a] px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-[#e2685a]/30 bg-[#e2685a]/10 text-[#a3352a] px-4 py-3 text-sm">
                    <p class="font-semibold mb-1">Revisa los siguientes campos:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
