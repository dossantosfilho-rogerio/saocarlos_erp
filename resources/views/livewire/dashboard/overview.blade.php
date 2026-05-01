<div class="space-y-6">

    {{-- Cabeçalho da página --}}
    <div>
        <h2 class="text-xl font-bold text-[#1A3A0A]">Visão Geral</h2>
        <p class="text-sm text-[#8A7A60] mt-0.5">Acompanhe os principais indicadores do seu negócio.</p>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        {{-- Vendas da Semana --}}
        <div class="bg-white rounded-xl border border-[#E0D8C8] shadow-sm p-5 flex items-start gap-4">
            <div class="h-11 w-11 rounded-lg bg-[#EAF4E2] flex items-center justify-center shrink-0">
                <svg class="h-5 w-5 text-[#2D5A1B]" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-[#8A7A60] font-medium uppercase tracking-wider">Vendas da Semana</p>
                <p class="text-2xl font-bold text-[#1A3A0A] mt-1">
                    R$ {{ number_format($kpis['vendas_semana'], 2, ',', '.') }}
                </p>
                <p class="text-xs text-[#A8C898] mt-1 flex items-center gap-1">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" /></svg>
                    Semana atual
                </p>
            </div>
        </div>

        {{-- Vendas do Mês --}}
        <div class="bg-white rounded-xl border border-[#E0D8C8] shadow-sm p-5 flex items-start gap-4">
            <div class="h-11 w-11 rounded-lg bg-[#EAF4E2] flex items-center justify-center shrink-0">
                <svg class="h-5 w-5 text-[#2D5A1B]" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-[#8A7A60] font-medium uppercase tracking-wider">Vendas do Mês</p>
                <p class="text-2xl font-bold text-[#1A3A0A] mt-1">
                    R$ {{ number_format($kpis['vendas_mes'], 2, ',', '.') }}
                </p>
                <p class="text-xs text-[#A8C898] mt-1 flex items-center gap-1">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" /></svg>
                    {{ now()->translatedFormat('F Y') }}
                </p>
            </div>
        </div>

        {{-- Volume Produzido --}}
        <div class="bg-white rounded-xl border border-[#E0D8C8] shadow-sm p-5 flex items-start gap-4">
            <div class="h-11 w-11 rounded-lg bg-[#FFF7ED] flex items-center justify-center shrink-0">
                <svg class="h-5 w-5 text-[#C2612A]" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-[#8A7A60] font-medium uppercase tracking-wider">Volume Produzido</p>
                <p class="text-2xl font-bold text-[#1A3A0A] mt-1">
                    {{ number_format($kpis['volume_kg'], 0, ',', '.') }} <span class="text-sm font-medium text-[#8A7A60]">kg</span>
                </p>
                <p class="text-xs text-[#D49A7A] mt-1">Mês atual</p>
            </div>
        </div>

        {{-- Lucro Estimado --}}
        <div class="bg-white rounded-xl border border-[#E0D8C8] shadow-sm p-5 flex items-start gap-4">
            <div class="h-11 w-11 rounded-lg bg-[#F0FDF4] flex items-center justify-center shrink-0">
                <svg class="h-5 w-5 text-[#16A34A]" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-[#8A7A60] font-medium uppercase tracking-wider">Lucro Estimado</p>
                <p class="text-2xl font-bold text-[#1A3A0A] mt-1">
                    R$ {{ number_format($kpis['lucro_estimado'], 2, ',', '.') }}
                </p>
                <p class="text-xs text-[#86EFAC] mt-1">Mês atual</p>
            </div>
        </div>

    </div>

    {{-- KPIs secundários --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        {{-- A Receber --}}
        <div class="bg-white rounded-xl border border-[#E0D8C8] shadow-sm p-5 flex items-start gap-4">
            <div class="h-11 w-11 rounded-lg bg-[#EFF6FF] flex items-center justify-center shrink-0">
                <svg class="h-5 w-5 text-[#2563EB]" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-[#8A7A60] font-medium uppercase tracking-wider">A Receber</p>
                <p class="text-2xl font-bold text-[#1A3A0A] mt-1">
                    R$ {{ number_format($kpis['a_receber'], 2, ',', '.') }}
                </p>
                <p class="text-xs text-[#93C5FD] mt-1">Contas em aberto</p>
            </div>
        </div>

        {{-- A Pagar --}}
        <div class="bg-white rounded-xl border border-[#E0D8C8] shadow-sm p-5 flex items-start gap-4">
            <div class="h-11 w-11 rounded-lg bg-[#FEF2F2] flex items-center justify-center shrink-0">
                <svg class="h-5 w-5 text-[#DC2626]" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-[#8A7A60] font-medium uppercase tracking-wider">A Pagar</p>
                <p class="text-2xl font-bold text-[#1A3A0A] mt-1">
                    R$ {{ number_format($kpis['a_pagar'], 2, ',', '.') }}
                </p>
                <p class="text-xs text-[#FCA5A5] mt-1">Contas em aberto</p>
            </div>
        </div>

        {{-- Produtos em Estoque --}}
        <div class="bg-white rounded-xl border border-[#E0D8C8] shadow-sm p-5 flex items-start gap-4">
            <div class="h-11 w-11 rounded-lg bg-[#F0FDF4] flex items-center justify-center shrink-0">
                <svg class="h-5 w-5 text-[#16A34A]" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-[#8A7A60] font-medium uppercase tracking-wider">Produtos em Estoque</p>
                <p class="text-2xl font-bold text-[#1A3A0A] mt-1">
                    {{ number_format($kpis['produtos_em_estoque'], 0, ',', '.') }}
                    <span class="text-sm font-medium text-[#8A7A60]">itens</span>
                </p>
                <p class="text-xs text-[#86EFAC] mt-1">Com saldo positivo</p>
            </div>
        </div>

        {{-- Insumos Ativos --}}
        <div class="bg-white rounded-xl border border-[#E0D8C8] shadow-sm p-5 flex items-start gap-4">
            <div class="h-11 w-11 rounded-lg bg-[#FFF7ED] flex items-center justify-center shrink-0">
                <svg class="h-5 w-5 text-[#C2612A]" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15M14.25 3.104c.251.023.501.05.75.082M19.8 15a2.25 2.25 0 0 1 .1 3.374l-1.05 1.05a2.25 2.25 0 0 1-3.182 0l-5.636-5.636a2.25 2.25 0 0 1 0-3.182l1.05-1.05a2.25 2.25 0 0 1 3.374.1L19.8 15Z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-[#8A7A60] font-medium uppercase tracking-wider">Insumos Ativos</p>
                <p class="text-2xl font-bold text-[#1A3A0A] mt-1">
                    {{ number_format($kpis['insumos_ativos'], 0, ',', '.') }}
                    <span class="text-sm font-medium text-[#8A7A60]">itens</span>
                </p>
                <p class="text-xs text-[#D49A7A] mt-1">Cadastrados e ativos</p>
            </div>
        </div>

    </div>

    {{-- Acesso Rápido --}}
    <div>
        <h3 class="text-sm font-semibold text-[#3A5C2A] mb-3 uppercase tracking-wider">Acesso Rápido</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3">
            @php
                $shortcuts = [
                    ['route' => 'vendas',        'label' => 'Vendas',         'color' => 'bg-[#2D5A1B]',   'icon' => 'cart'],
                    ['route' => 'compras',       'label' => 'Compras/Despesas',        'color' => 'bg-[#8C4B27]',   'icon' => 'bag'],
                    ['route' => 'estoque',        'label' => 'Estoque',        'color' => 'bg-[#3D7A25]',   'icon' => 'cube'],
                    ['route' => 'ficha-tecnica',  'label' => 'Produtos/Insumos',  'color' => 'bg-[#A0522D]',   'icon' => 'doc'],
                    ['route' => 'clientes',       'label' => 'Clientes',       'color' => 'bg-[#1E6B8A]',   'icon' => 'users'],
                    ['route' => 'fornecedores',   'label' => 'Fornecedores',   'color' => 'bg-[#5B6B2A]',   'icon' => 'truck'],
                    ['route' => 'contas',         'label' => 'Contas',         'color' => 'bg-[#7A3D8A]',   'icon' => 'banknotes'],
                ];
            @endphp

            @foreach($shortcuts as $sc)
                <a href="{{ route($sc['route']) }}"
                   class="{{ $sc['color'] }} rounded-xl p-4 flex flex-col items-center gap-2 text-white hover:brightness-110 transition shadow-sm hover:shadow-md">
                    @if($sc['icon'] === 'cart')
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
                    @elseif($sc['icon'] === 'cube')
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
                    @elseif($sc['icon'] === 'bag')
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-3 0h15l-1.125 9.375A2.25 2.25 0 0 1 16.14 21H7.86a2.25 2.25 0 0 1-2.235-1.125L4.5 10.5Z" /></svg>
                    @elseif($sc['icon'] === 'beaker')
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15M14.25 3.104c.251.023.501.05.75.082M19.8 15a2.25 2.25 0 0 1 .1 3.374l-1.05 1.05a2.25 2.25 0 0 1-3.182 0l-5.636-5.636a2.25 2.25 0 0 1 0-3.182l1.05-1.05a2.25 2.25 0 0 1 3.374.1L19.8 15Z" /></svg>
                    @elseif($sc['icon'] === 'doc')
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                    @elseif($sc['icon'] === 'users')
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg>
                    @elseif($sc['icon'] === 'truck')
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                    @elseif($sc['icon'] === 'banknotes')
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>
                    @endif
                    <span class="text-xs font-medium text-center leading-tight">{{ $sc['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Avisos / Estado vazio --}}
    @if($kpis['vendas_mes'] == 0 && $kpis['produtos_em_estoque'] == 0 && $kpis['insumos_ativos'] == 0)
    <div class="bg-white rounded-xl border border-[#E0D8C8] shadow-sm p-6 flex items-start gap-4">
        <div class="h-10 w-10 rounded-full bg-[#EAF4E2] flex items-center justify-center shrink-0">
            <svg class="h-5 w-5 text-[#2D5A1B]" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-[#1A3A0A]">Sistema pronto para uso</p>
            <p class="text-sm text-[#8A7A60] mt-0.5">
                Comece cadastrando seus <a href="{{ route('clientes') }}" class="text-[#2D5A1B] underline underline-offset-2 hover:text-[#1C3E12]">clientes</a>,
                <a href="{{ route('fornecedores') }}" class="text-[#2D5A1B] underline underline-offset-2 hover:text-[#1C3E12]">fornecedores</a> e
                <a href="{{ route('ficha-tecnica') }}" class="text-[#2D5A1B] underline underline-offset-2 hover:text-[#1C3E12]">insumos</a>
                para que os indicadores sejam preenchidos automaticamente.
            </p>
        </div>
    </div>
    @endif

</div>
