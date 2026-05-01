<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'São Carlos ERP') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&family=montserrat:600,700,800" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    @livewireStyles
</head>
<body class="bg-[#F0EBE0] font-sans antialiased">

    <div class="min-h-screen lg:flex" x-data="{ sidebarOpen: false }">

        <div x-cloak x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-30 bg-[#1A1A1A]/50 lg:hidden" @click="sidebarOpen = false"></div>

        {{-- Sidebar --}}
        <aside
            x-cloak
            class="fixed inset-y-0 left-0 z-40 flex w-72 max-w-[85vw] -translate-x-full flex-col bg-[#2D5A1B] text-white transition-transform duration-300 ease-in-out lg:static lg:z-auto lg:w-64 lg:max-w-none lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0 shadow-2xl lg:shadow-none' : '-translate-x-full'"
        >
            {{-- Logo / Marca --}}
            <div class="flex items-center gap-3 border-b border-white/10 px-4 py-5">
                @if(file_exists(public_path('images/logo.jpg')))
                    <img src="{{ asset('images/logo.jpg') }}" alt="São Carlos"
                         class="h-9 w-9 rounded-full object-cover shrink-0 ring-2 ring-white/30">
                @else
                    <div class="h-9 w-9 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                        <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3C7.03 3 3 7.03 3 12s4.03 9 9 9 9-4.03 9-9-4.03-9-9-9z"/>
                        </svg>
                    </div>
                @endif
                <div class="min-w-0">
                    <p class="text-sm font-bold tracking-widest uppercase leading-tight" style="font-family: 'Montserrat', sans-serif;">São Carlos</p>
                    <p class="text-[10px] text-white/50 uppercase tracking-wider">ERP</p>
                </div>

                <button @click="sidebarOpen = false" class="ml-auto rounded-md p-1 text-white/70 hover:bg-white/10 hover:text-white lg:hidden">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 overflow-y-auto py-4 space-y-0.5 px-2">

                @php
                    $navItems = [
                        ['route' => 'dashboard',    'icon' => 'chart-bar',      'label' => 'Dashboard'],
                        ['route' => 'vendas',        'icon' => 'shopping-cart',  'label' => 'Vendas'],
                        ['route' => 'compras',       'icon' => 'shopping-bag',   'label' => 'Compras/Despesas'],
                        ['route' => 'estoque',       'icon' => 'cube',           'label' => 'Estoque'],
                        ['route' => 'ficha-tecnica', 'icon' => 'document-text',  'label' => 'Produtos/Insumos'],
                        ['route' => 'clientes',      'icon' => 'user-group',     'label' => 'Clientes'],
                        ['route' => 'fornecedores',  'icon' => 'truck',          'label' => 'Fornecedores'],
                        ['route' => 'contas',        'icon' => 'banknotes',      'label' => 'Contas'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    @php
                        $isActive = request()->routeIs($item['route']);
                    @endphp
                    <a href="{{ route($item['route']) }}"
                       title="{{ $item['label'] }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group
                              {{ $isActive
                                  ? 'bg-white/15 text-white'
                                  : 'text-white/70 hover:bg-white/10 hover:text-white' }}"
                       @click="sidebarOpen = false">

                        {{-- Ícones --}}
                        @if($item['icon'] === 'chart-bar')
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
                        @elseif($item['icon'] === 'shopping-cart')
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
                        @elseif($item['icon'] === 'shopping-bag')
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-3 0h15l-1.125 9.375A2.25 2.25 0 0 1 16.14 21H7.86a2.25 2.25 0 0 1-2.235-1.125L4.5 10.5Z" /></svg>
                        @elseif($item['icon'] === 'cube')
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
                        @elseif($item['icon'] === 'beaker')
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15M14.25 3.104c.251.023.501.05.75.082M19.8 15a2.25 2.25 0 0 1 .1 3.374l-1.05 1.05a2.25 2.25 0 0 1-3.182 0l-5.636-5.636a2.25 2.25 0 0 1 0-3.182l1.05-1.05a2.25 2.25 0 0 1 3.374.1L19.8 15ZM5 14.5a2.25 2.25 0 0 0 .1 3.374l1.05 1.05a2.25 2.25 0 0 0 3.182 0" /></svg>
                        @elseif($item['icon'] === 'document-text')
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                        @elseif($item['icon'] === 'user-group')
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg>
                        @elseif($item['icon'] === 'truck')
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                        @elseif($item['icon'] === 'banknotes')
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>
                        @endif

                        <span class="truncate">
                            {{ $item['label'] }}
                        </span>

                        @if($isActive)
                            <span class="ml-auto h-1.5 w-1.5 rounded-full bg-white shrink-0"></span>
                        @endif
                    </a>
                @endforeach

            </nav>

            {{-- Usuário / Logout --}}
            <div class="border-t border-white/10 px-2 py-3">
                <div class="flex items-center gap-3 rounded-lg px-2 py-2">
                    <div class="h-8 w-8 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold text-white shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-white/50 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Sair"
                                class="text-white/50 hover:text-white transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Conteúdo principal --}}
        <div class="flex min-h-screen flex-1 flex-col overflow-hidden lg:pl-0">

            {{-- Topbar --}}
            <header class="shrink-0 border-b border-[#E0D8C8] bg-white px-4 py-3 sm:px-6">
                <div class="flex items-center gap-3 sm:gap-4">
                {{-- Toggle sidebar --}}
                <button @click="sidebarOpen = !sidebarOpen"
                        class="rounded-md p-2 text-[#4A6B3A] transition hover:bg-[#F5F1E8] hover:text-[#2D5A1B] lg:hidden">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <div class="flex-1">
                    <h1 class="text-sm font-semibold text-[#1A3A0A] sm:text-base">{{ $title ?? 'Dashboard' }}</h1>
                    <p class="text-[11px] text-[#8A7A60] sm:text-xs">{{ now()->translatedFormat('l, d \d\e F \d\e Y') }}</p>
                </div>

                <div class="hidden items-center gap-2 text-xs text-[#8A7A60] sm:flex">
                    <svg class="h-4 w-4 text-[#2D5A1B]" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    {{ now()->format('H:i') }}
                </div>
                </div>
            </header>

            {{-- Página --}}
            <main class="flex-1 overflow-y-auto p-4 sm:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
