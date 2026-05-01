<div class="relative space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-[#1A3A0A]">Compras</h2>
            <p class="text-sm text-[#8A7A60]">Lancamento de compras de insumos, geracao de contas a pagar e custo medio.</p>
        </div>

        <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
            <div class="w-full sm:w-80">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    wire:loading.attr="disabled"
                    wire:target="search"
                    placeholder="Buscar compra por numero ou fornecedor"
                    class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2.5 text-sm text-[#1A3A0A] focus:border-[#2D5A1B] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/20"
                >
            </div>

            <button
                type="button"
                wire:click="openCreateModal"
                wire:loading.attr="disabled"
                wire:target="openCreateModal"
                class="inline-flex items-center justify-center rounded-lg bg-[#2D5A1B] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#234716] disabled:cursor-not-allowed disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="openCreateModal">Nova Compra</span>
                <span wire:loading.inline wire:target="openCreateModal">Abrindo...</span>
            </button>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-lg border border-[#B5D9A5] bg-[#ECF8E7] px-4 py-3 text-sm text-[#234A14]">
            {{ session('status') }}
        </div>
    @endif

    <section class="rounded-xl border border-[#E0D8C8] bg-white p-5 shadow-sm">
        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-[#3A5C2A]">Historico de Compras</h3>

        <div class="overflow-x-auto rounded-lg border border-[#EFE7D9]" wire:loading.class="opacity-60" wire:target="search,gotoPage,previousPage,nextPage,salvarCompra">
            <table class="min-w-full divide-y divide-[#EFE7D9] text-sm">
                <thead class="bg-[#FAF7F0]">
                    <tr class="text-left text-xs uppercase tracking-wider text-[#8A7A60]">
                        <th class="px-3 py-3 font-semibold">Compra</th>
                        <th class="px-3 py-3 font-semibold">Fornecedor</th>
                        <th class="px-3 py-3 font-semibold">Vencimento</th>
                        <th class="px-3 py-3 font-semibold">Conta</th>
                        <th class="px-3 py-3 font-semibold text-right">Valor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EFE7D9] bg-white">
                    @forelse($compras as $compra)
                        @php
                            $parcelas = $compra->contasPagar;
                            $totalParcelas = $parcelas->count();
                            $pagas = $parcelas->where('status', 'pago')->count();
                            $statusResumo = $totalParcelas === 0
                                ? 'SEM CONTA'
                                : ($pagas === $totalParcelas
                                    ? 'PAGO'
                                    : ($pagas > 0 ? 'PARCIAL' : 'PENDENTE'));
                            $proximoVencimento = $parcelas
                                ->where('status', '!=', 'pago')
                                ->sortBy('data_vencimento')
                                ->first()?->data_vencimento;
                        @endphp
                        @php
                            $categoriasMap = \App\Livewire\Compras\Index::CATEGORIAS_DESPESA;
                        @endphp
                        <tr class="hover:bg-[#FCFAF4]">
                            <td class="px-3 py-3 align-top">
                                <p class="font-semibold text-[#1A3A0A]">#{{ $compra->id }}</p>
                                <p class="text-xs text-[#8A7A60]">{{ \Carbon\Carbon::parse($compra->data_compra)->format('d/m/Y H:i') }}</p>
                                @if($compra->tipo === 'despesa')
                                    <span class="mt-0.5 inline-flex items-center rounded-full bg-[#FDF0EA] px-2 py-0.5 text-xs font-medium text-[#8C4B27]">
                                        {{ $categoriasMap[$compra->categoria_despesa] ?? 'Despesa' }}
                                    </span>
                                @else
                                    <span class="mt-0.5 inline-flex items-center rounded-full bg-[#EAF4E2] px-2 py-0.5 text-xs font-medium text-[#2D5A1B]">Insumos</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 align-top text-[#4F5D45]">{{ $compra->fornecedor?->nome ?: 'Nao informado' }}</td>
                            <td class="px-3 py-3 align-top text-[#4F5D45]">{{ $proximoVencimento?->format('d/m/Y') ?: '-' }}</td>
                            <td class="px-3 py-3 align-top">
                                <span class="inline-flex rounded-full bg-[#FFF7ED] px-2 py-0.5 text-xs font-medium text-[#A65A2A]">
                                    {{ $statusResumo }}{{ $totalParcelas > 1 ? ' (' . $totalParcelas . 'x)' : '' }}
                                </span>
                            </td>
                            <td class="px-3 py-3 align-top text-right font-semibold text-[#1A3A0A]">R$ {{ number_format((float) $compra->valor_total, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-sm text-[#8A7A60]">
                                Nenhuma compra registrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $compras->links() }}</div>
    </section>

    @if($showFormModal)
        <div class="fixed inset-0 z-40 bg-[#1A1A1A]/50" wire:click="closeFormModal"></div>

        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <section class="max-h-[95vh] w-full max-w-5xl overflow-y-auto rounded-xl border border-[#E0D8C8] bg-white p-5 shadow-xl">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-[#3A5C2A]">Novo Lançamento</h3>
                        {{-- Toggle tipo --}}
                        <div class="mt-2 flex gap-1 rounded-lg border border-[#DCCFB7] bg-[#FAF7F0] p-0.5 w-fit">
                            <button type="button" wire:click="$set('form.tipo', 'compra')"
                                class="rounded-md px-3 py-1.5 text-xs font-semibold transition
                                    {{ $form['tipo'] === 'compra' ? 'bg-[#2D5A1B] text-white shadow-sm' : 'text-[#5A4A38] hover:bg-white' }}">
                                Compra de Insumos
                            </button>
                            <button type="button" wire:click="$set('form.tipo', 'despesa')"
                                class="rounded-md px-3 py-1.5 text-xs font-semibold transition
                                    {{ $form['tipo'] === 'despesa' ? 'bg-[#8C4B27] text-white shadow-sm' : 'text-[#5A4A38] hover:bg-white' }}">
                                Despesa
                            </button>
                        </div>
                    </div>
                    <button type="button" wire:click="closeFormModal" class="rounded-md px-2 py-1 text-[#8A7A60] hover:bg-[#F7F3EA]">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="salvarCompra" class="space-y-4" wire:click.stop>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Data da Compra</label>
                            <input type="datetime-local" wire:model="form.data_compra" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm">
                            @error('form.data_compra') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Fornecedor (Opcional)</label>
                            <select wire:model="form.fornecedor_id" class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm">
                                <option value="">Sem fornecedor</option>
                                @foreach($fornecedoresAtivos as $fornecedor)
                                    <option value="{{ $fornecedor->id }}">{{ $fornecedor->nome }}</option>
                                @endforeach
                            </select>
                            @error('form.fornecedor_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Condicao de Pagamento</label>
                            <select wire:model.live="form.condicao_pagamento" class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm">
                                <option value="a_vista">A vista</option>
                                <option value="parcelado">Parcelado</option>
                            </select>
                            @error('form.condicao_pagamento') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 rounded-lg border border-[#EFE7D9] bg-[#FAF7F0] p-3">
                        @if($form['condicao_pagamento'] === 'a_vista')
                            <div>
                                <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Situacao</label>
                                <select wire:model="form.conta_paga_no_ato" class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm">
                                    <option value="0">Gerar conta em aberto</option>
                                    <option value="1">Ja pago no ato</option>
                                </select>
                                @error('form.conta_paga_no_ato') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            @if(! (bool) $form['conta_paga_no_ato'])
                                <div>
                                    <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Vencimento</label>
                                    <input type="date" wire:model="form.data_primeiro_vencimento" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm">
                                    @error('form.data_primeiro_vencimento') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            @endif
                        @endif

                        @if($form['condicao_pagamento'] === 'parcelado')
                            <div>
                                <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Quantidade de Parcelas</label>
                                <input type="number" min="2" max="60" wire:model="form.quantidade_parcelas" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm">
                                @error('form.quantidade_parcelas') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Primeiro Vencimento</label>
                                <input type="date" wire:model="form.data_primeiro_vencimento" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm">
                                @error('form.data_primeiro_vencimento') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Intervalo entre Parcelas (dias)</label>
                                <input type="number" min="1" max="365" wire:model="form.intervalo_parcelas_dias" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm">
                                @error('form.intervalo_parcelas_dias') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Observacoes</label>
                        <textarea rows="2" wire:model="form.observacoes" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm"></textarea>
                        @error('form.observacoes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    @if($form['tipo'] === 'compra')
                    <div class="rounded-lg border border-[#EFE7D9] bg-[#FAF7F0] p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-sm font-semibold text-[#1A3A0A]">Itens da Compra</p>
                            <button type="button" wire:click="addItemLinha" class="rounded-md border border-[#DCCFB7] px-2.5 py-1.5 text-xs font-medium text-[#4F5D45] hover:bg-[#F7F3EA]">Adicionar Linha</button>
                        </div>

                        <div class="space-y-2">
                            @foreach($itensCompra as $index => $item)
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-12">
                                    <div class="sm:col-span-6">
                                        <select wire:model="itensCompra.{{ $index }}.insumo_id" class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm">
                                            <option value="">Selecione o insumo...</option>
                                            @foreach($insumosAtivos as $insumo)
                                                <option value="{{ $insumo->id }}">
                                                    {{ $insumo->nome }}{{ $insumo->codigo ? ' - ' . $insumo->codigo : '' }}
                                                    | Custo medio atual: R$ {{ number_format((float) $insumo->custo_unitario, 4, ',', '.') }}/{{ $insumo->unidade }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <input type="number" step="0.0001" wire:model="itensCompra.{{ $index }}.quantidade" placeholder="Quantidade" class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm">
                                    </div>
                                    <div class="sm:col-span-3">
                                        <input type="number" step="0.0001" wire:model="itensCompra.{{ $index }}.valor_unitario" placeholder="Valor unitario" class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm">
                                    </div>
                                    <div class="sm:col-span-1">
                                        <button type="button" wire:click="removeItemLinha({{ $index }})" class="w-full rounded-lg border border-[#F2C8C3] px-2 py-2 text-xs font-medium text-[#9A4030] hover:bg-[#FDECEA]">X</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @error('itensCompra') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    @endif

                    @if($form['tipo'] === 'despesa')
                    <div class="rounded-lg border border-[#F5E0D5] bg-[#FDF7F4] p-4 space-y-3">
                        <p class="text-sm font-semibold text-[#8C4B27]">Dados da Despesa</p>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Categoria</label>
                                <select wire:model="form.categoria_despesa" class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm">
                                    <option value="">Selecione a categoria...</option>
                                    @foreach($categoriasDespesa as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('form.categoria_despesa') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Valor (R$)</label>
                                <input type="number" step="0.01" min="0.01" wire:model="form.valor_despesa" placeholder="0,00" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm">
                                @error('form.valor_despesa') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Descrição</label>
                            <input type="text" wire:model="form.descricao_despesa" placeholder="Ex: Troca de óleo veículo XYZ" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm">
                            @error('form.descricao_despesa') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 gap-3 rounded-lg border border-[#DCCFB7] bg-[#FFFEFA] p-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Valor Total</p>
                            <p class="mt-1 text-sm font-semibold text-[#1A3A0A]">
                                @if($form['tipo'] === 'despesa')
                                    R$ {{ number_format((float) ($form['valor_despesa'] ?: 0), 2, ',', '.') }}
                                @else
                                    R$ {{ number_format($this->valorTotalCompraCalculado(), 2, ',', '.') }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Contas a Pagar</p>
                            <p class="mt-1 text-sm font-semibold text-[#1A3A0A]">
                                @if($form['condicao_pagamento'] === 'parcelado')
                                    {{ (int) ($form['quantidade_parcelas'] ?: 0) }} parcelas
                                @elseif((bool) $form['conta_paga_no_ato'])
                                    Pago no ato
                                @else
                                    1 conta em aberto
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="closeFormModal" class="rounded-lg border border-[#DCCFB7] px-4 py-2 text-sm">Cancelar</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="salvarCompra" class="rounded-lg bg-[#2D5A1B] px-4 py-2 text-sm font-semibold text-white disabled:opacity-60">
                            <span wire:loading.remove wire:target="salvarCompra">Salvar Compra</span>
                            <span wire:loading.inline wire:target="salvarCompra">Salvando...</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    @endif

    <div wire:loading.flex class="fixed inset-0 z-[60] items-center justify-center bg-[#1A1A1A]/30 backdrop-blur-[1px]">
        <div class="flex items-center gap-3 rounded-xl border border-[#DCCFB7] bg-white px-4 py-3 shadow-lg">
            <svg class="h-5 w-5 animate-spin text-[#2D5A1B]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="text-sm font-medium text-[#1A3A0A]">Carregando...</span>
        </div>
    </div>
</div>
