<div class="space-y-5">

    {{-- Cabeçalho --}}
    <div>
        <h2 class="text-xl font-bold text-[#1A3A0A]">Contas</h2>
        <p class="text-sm text-[#8A7A60] mt-0.5">Gerencie contas a pagar e a receber.</p>
    </div>

    {{-- Abas --}}
    <div class="flex border-b border-[#DCCFB7]">
        <button wire:click="$set('aba','a_receber')"
            class="px-5 py-2.5 text-sm font-semibold transition-colors border-b-2 -mb-px
                {{ $aba === 'a_receber'
                    ? 'border-[#2D5A1B] text-[#2D5A1B]'
                    : 'border-transparent text-[#8A7A60] hover:text-[#1A3A0A]' }}">
            A Receber
        </button>
        <button wire:click="$set('aba','a_pagar')"
            class="px-5 py-2.5 text-sm font-semibold transition-colors border-b-2 -mb-px
                {{ $aba === 'a_pagar'
                    ? 'border-[#8C4B27] text-[#8C4B27]'
                    : 'border-transparent text-[#8A7A60] hover:text-[#1A3A0A]' }}">
            A Pagar
        </button>
    </div>

    {{-- Filtros --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-[#A89A80]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="{{ $aba === 'a_receber' ? 'Buscar por descrição ou cliente…' : 'Buscar por descrição ou fornecedor…' }}"
                class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-[#DCCFB7] bg-white text-[#1A3A0A] placeholder-[#A89A80] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/30" />
        </div>
        <select wire:model.live="filtroStatus"
            class="text-sm rounded-lg border border-[#DCCFB7] bg-white text-[#1A3A0A] px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/30">
            <option value="">Todos os status</option>
            <option value="pendente">Pendente</option>
            <option value="parcial">Pago parcialmente</option>
            <option value="pago">Pago</option>
        </select>
    </div>

    {{-- Totalizadores --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-[#E0D8C8] p-4">
            <p class="text-xs text-[#8A7A60] uppercase tracking-wider font-medium">Total em aberto (filtro)</p>
            <p class="text-xl font-bold mt-1 {{ $aba === 'a_receber' ? 'text-[#2563EB]' : 'text-[#DC2626]' }}">
                R$ {{ number_format($totalAberto, 2, ',', '.') }}
            </p>
        </div>
        <div class="bg-white rounded-xl border border-[#E0D8C8] p-4">
            <p class="text-xs text-[#8A7A60] uppercase tracking-wider font-medium">Valor original (filtro)</p>
            <p class="text-xl font-bold text-[#1A3A0A] mt-1">
                R$ {{ number_format($totalOriginal, 2, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Tabela --}}
    <div class="bg-white rounded-xl border border-[#E0D8C8] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-[#F6F2EA] border-b border-[#E0D8C8]">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#5A4A38] uppercase tracking-wider">Descrição</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#5A4A38] uppercase tracking-wider">
                            {{ $aba === 'a_receber' ? 'Cliente' : 'Fornecedor' }}
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-[#5A4A38] uppercase tracking-wider">Vencimento</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-[#5A4A38] uppercase tracking-wider">Valor Original</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-[#5A4A38] uppercase tracking-wider">Em Aberto</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-[#5A4A38] uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-[#5A4A38] uppercase tracking-wider">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F0EBE0]">
                    @forelse($contas as $conta)
                        @php
                            $vencida = $conta->status !== 'pago'
                                && $conta->data_vencimento->isPast();
                        @endphp
                        <tr class="hover:bg-[#FAFAF7] transition-colors {{ $vencida ? 'bg-red-50/40' : '' }}">
                            <td class="px-4 py-3 text-[#1A3A0A] font-medium max-w-xs truncate">
                                {{ $conta->descricao }}
                            </td>
                            <td class="px-4 py-3 text-[#4A3A28]">
                                @if($aba === 'a_receber')
                                    {{ $conta->cliente->nome ?? '—' }}
                                @else
                                    {{ $conta->fornecedor->nome ?? '—' }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="{{ $vencida ? 'text-red-600 font-semibold' : 'text-[#4A3A28]' }}">
                                    {{ $conta->data_vencimento->format('d/m/Y') }}
                                </span>
                                @if($vencida)
                                    <span class="ml-1 text-xs text-red-500">(vencida)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-[#4A3A28]">
                                R$ {{ number_format($conta->valor_original, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold {{ $conta->status !== 'pago' ? ($aba === 'a_receber' ? 'text-[#2563EB]' : 'text-[#DC2626]') : 'text-[#8A7A60]' }}">
                                R$ {{ number_format($conta->valor_aberto, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($conta->status === 'pago')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 0 1 0 1.414l-8 8a1 1 0 0 1-1.414 0l-4-4a1 1 0 1 1 1.414-1.414L8 12.586l7.293-7.293a1 1 0 0 1 1.414 0Z" clip-rule="evenodd"/></svg>
                                        Pago
                                    </span>
                                @elseif($conta->status === 'parcial')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                        Parcial
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $vencida ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">
                                        Pendente
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($conta->status !== 'pago')
                                    <button
                                        wire:click="abrirBaixa({{ $conta->id }}, '{{ $aba === 'a_receber' ? 'receber' : 'pagar' }}')"
                                        class="inline-flex items-center gap-1 text-xs font-medium px-3 py-1.5 rounded-lg transition
                                            {{ $aba === 'a_receber'
                                                ? 'bg-[#EAF4E2] text-[#2D5A1B] hover:bg-[#2D5A1B] hover:text-white'
                                                : 'bg-[#FDF0EA] text-[#8C4B27] hover:bg-[#8C4B27] hover:text-white' }}">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                                        </svg>
                                        Baixar
                                    </button>
                                @else
                                    <span class="text-xs text-[#B0A090]">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-[#A89A80] text-sm">
                                Nenhuma conta encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contas->hasPages())
            <div class="px-4 py-3 border-t border-[#F0EBE0]">
                {{ $contas->links() }}
            </div>
        @endif
    </div>

    {{-- Modal de Baixa --}}
    @if($showBaixaModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-data x-init="$el.querySelector('#baixa-modal').focus()">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="fecharBaixaModal"></div>

        {{-- Card --}}
        <div id="baixa-modal" tabindex="-1"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md ring-1
                {{ $baixaTipo === 'receber' ? 'ring-[#2D5A1B]/20' : 'ring-[#8C4B27]/20' }}">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#F0EBE0]">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-lg flex items-center justify-center
                        {{ $baixaTipo === 'receber' ? 'bg-[#EAF4E2]' : 'bg-[#FDF0EA]' }}">
                        <svg class="h-5 w-5 {{ $baixaTipo === 'receber' ? 'text-[#2D5A1B]' : 'text-[#8C4B27]' }}"
                             fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-[#1A3A0A]">
                        Registrar {{ $baixaTipo === 'receber' ? 'Recebimento' : 'Pagamento' }}
                    </h3>
                </div>
                <button wire:click="fecharBaixaModal"
                    class="h-8 w-8 flex items-center justify-center rounded-lg text-[#8A7A60] hover:bg-[#F0EBE0] transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4">
                {{-- Info da conta --}}
                <div class="bg-[#FAF7F0] rounded-xl p-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-[#8A7A60]">Descrição</span>
                        <span class="font-medium text-[#1A3A0A] text-right max-w-xs truncate">{{ $baixaInfo['descricao'] ?? '' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#8A7A60]">{{ $baixaTipo === 'receber' ? 'Cliente' : 'Fornecedor' }}</span>
                        <span class="font-medium text-[#1A3A0A]">{{ $baixaInfo['entidade'] ?? '' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#8A7A60]">Vencimento</span>
                        <span class="font-medium text-[#1A3A0A]">{{ $baixaInfo['vencimento'] ?? '' }}</span>
                    </div>
                    <div class="flex justify-between border-t border-[#DCCFB7] pt-2 mt-2">
                        <span class="text-[#8A7A60] font-medium">Saldo em Aberto</span>
                        <span class="font-bold text-lg {{ $baixaTipo === 'receber' ? 'text-[#2563EB]' : 'text-[#DC2626]' }}">
                            R$ {{ number_format($baixaInfo['valor_aberto'] ?? 0, 2, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Valor a baixar --}}
                <div>
                    <label class="block text-xs font-semibold text-[#3A5C2A] uppercase tracking-wider mb-1.5">
                        Valor {{ $baixaTipo === 'receber' ? 'Recebido' : 'Pago' }}
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-sm text-[#8A7A60] pointer-events-none">R$</span>
                        <input wire:model="baixaValorPago" type="number" step="0.01" min="0.01"
                               max="{{ $baixaInfo['valor_aberto'] ?? 0 }}"
                               placeholder="0,00"
                               class="w-full pl-9 pr-4 py-2.5 text-sm rounded-lg border
                                    @error('baixaValorPago') border-red-400 @else border-[#DCCFB7] @enderror
                                    bg-white text-[#1A3A0A] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/30" />
                    </div>
                    @error('baixaValorPago')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-[#A89A80]">
                        Baixa total: R$ {{ number_format($baixaInfo['valor_aberto'] ?? 0, 2, ',', '.') }}
                        — ou informe um valor parcial.
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-[#F0EBE0] flex gap-3 justify-end">
                <button wire:click="fecharBaixaModal"
                    class="px-4 py-2 text-sm font-medium text-[#5A4A38] bg-[#F0EBE0] rounded-lg hover:bg-[#E5DDD0] transition">
                    Cancelar
                </button>
                <button wire:click="registrarBaixa" wire:loading.attr="disabled"
                    class="px-5 py-2 text-sm font-semibold text-white rounded-lg transition disabled:opacity-60
                        {{ $baixaTipo === 'receber'
                            ? 'bg-[#2D5A1B] hover:bg-[#1C3E12]'
                            : 'bg-[#8C4B27] hover:bg-[#6A3820]' }}">
                    <span wire:loading wire:target="registrarBaixa" class="inline-flex items-center gap-1.5">
                        <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z"/>
                        </svg>
                        Salvando…
                    </span>
                    <span wire:loading.remove wire:target="registrarBaixa">
                        Confirmar {{ $baixaTipo === 'receber' ? 'Recebimento' : 'Pagamento' }}
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
