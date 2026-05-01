<div class="relative space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-[#1A3A0A]">Vendas</h2>
            <p class="text-sm text-[#8A7A60]">Lancamento de vendas com cliente, produtos e contas a receber.</p>
        </div>

        <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
            <div class="w-full sm:w-80">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    wire:loading.attr="disabled"
                    wire:target="search"
                    placeholder="Buscar venda por numero ou cliente"
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
                <span wire:loading.remove wire:target="openCreateModal">Nova Venda</span>
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
        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-[#3A5C2A]">Historico de Vendas</h3>

        <div class="overflow-x-auto rounded-lg border border-[#EFE7D9]" wire:loading.class="opacity-60" wire:target="search,gotoPage,previousPage,nextPage,salvarVenda">
            <table class="min-w-full divide-y divide-[#EFE7D9] text-sm">
                <thead class="bg-[#FAF7F0]">
                    <tr class="text-left text-xs uppercase tracking-wider text-[#8A7A60]">
                        <th class="px-3 py-3 font-semibold">Venda</th>
                        <th class="px-3 py-3 font-semibold">Cliente</th>
                        <th class="px-3 py-3 font-semibold">Proximo Vencimento</th>
                        <th class="px-3 py-3 font-semibold">Contas</th>
                        <th class="px-3 py-3 font-semibold text-right">Valor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EFE7D9] bg-white">
                    @forelse($vendas as $venda)
                        @php
                            $parcelas = $venda->contasReceber;
                            $totalParcelas = $parcelas->count();
                            $recebidas = $parcelas->where('status', 'recebido')->count();
                            $statusResumo = $totalParcelas === 0
                                ? 'SEM CONTA'
                                : ($recebidas === $totalParcelas
                                    ? 'RECEBIDO'
                                    : ($recebidas > 0 ? 'PARCIAL' : 'PENDENTE'));
                            $proximoVencimento = $parcelas
                                ->where('status', '!=', 'recebido')
                                ->sortBy('data_vencimento')
                                ->first()?->data_vencimento;
                        @endphp
                        <tr class="hover:bg-[#FCFAF4]">
                            <td class="px-3 py-3 align-top">
                                <p class="font-semibold text-[#1A3A0A]">#{{ $venda->id }}</p>
                                <p class="text-xs text-[#8A7A60]">{{ \Carbon\Carbon::parse($venda->data_venda)->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="px-3 py-3 align-top text-[#4F5D45]">{{ $venda->cliente->nome }}</td>
                            <td class="px-3 py-3 align-top text-[#4F5D45]">{{ $proximoVencimento?->format('d/m/Y') ?: '-' }}</td>
                            <td class="px-3 py-3 align-top">
                                <span class="inline-flex rounded-full bg-[#FFF7ED] px-2 py-0.5 text-xs font-medium text-[#A65A2A]">
                                    {{ $statusResumo }}{{ $totalParcelas > 1 ? ' (' . $totalParcelas . 'x)' : '' }}
                                </span>
                            </td>
                            <td class="px-3 py-3 align-top text-right font-semibold text-[#1A3A0A]">R$ {{ number_format((float) $venda->valor_total, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-sm text-[#8A7A60]">
                                Nenhuma venda registrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $vendas->links() }}</div>
    </section>

    @if($showFormModal)
        <div class="fixed inset-0 z-40 bg-[#1A1A1A]/50" wire:click="closeFormModal"></div>

        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <section class="max-h-[95vh] w-full max-w-5xl overflow-y-auto rounded-xl border border-[#E0D8C8] bg-white p-5 shadow-xl">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-[#3A5C2A]">Nova Venda</h3>
                    <button type="button" wire:click="closeFormModal" class="rounded-md px-2 py-1 text-[#8A7A60] hover:bg-[#F7F3EA]">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="salvarVenda" class="space-y-4" wire:click.stop>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Data da Venda</label>
                            <input type="datetime-local" wire:model="form.data_venda" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm">
                            @error('form.data_venda') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Cliente</label>
                            <select wire:model="form.cliente_id" class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm">
                                <option value="">Selecione o cliente</option>
                                @foreach($clientesAtivos as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                @endforeach
                            </select>
                            @error('form.cliente_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Condicao de Recebimento</label>
                            <select wire:model.live="form.condicao_recebimento" class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm">
                                <option value="a_vista">A vista</option>
                                <option value="parcelado">Parcelado</option>
                            </select>
                            @error('form.condicao_recebimento') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 rounded-lg border border-[#EFE7D9] bg-[#FAF7F0] p-3">
                        @if($form['condicao_recebimento'] === 'a_vista')
                            <div>
                                <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Situacao</label>
                                <select wire:model="form.conta_recebida_no_ato" class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm">
                                    <option value="0">Gerar conta em aberto</option>
                                    <option value="1">Recebido no ato</option>
                                </select>
                                @error('form.conta_recebida_no_ato') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            @if(! (bool) $form['conta_recebida_no_ato'])
                                <div>
                                    <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Vencimento</label>
                                    <input type="date" wire:model="form.data_primeiro_vencimento" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm">
                                    @error('form.data_primeiro_vencimento') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            @endif
                        @endif

                        @if($form['condicao_recebimento'] === 'parcelado')
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

                    <div class="rounded-lg border border-[#EFE7D9] bg-[#FAF7F0] p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-sm font-semibold text-[#1A3A0A]">Itens da Venda</p>
                            <button type="button" wire:click="addItemLinha" class="rounded-md border border-[#DCCFB7] px-2.5 py-1.5 text-xs font-medium text-[#4F5D45] hover:bg-[#F7F3EA]">Adicionar Linha</button>
                        </div>

                        <div class="space-y-2">
                            @foreach($itensVenda as $index => $item)
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-12">
                                    <div class="sm:col-span-6">
                                        <select wire:model="itensVenda.{{ $index }}.produto_id" class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm">
                                            <option value="">Selecione o produto...</option>
                                            @foreach($produtosAtivos as $produto)
                                                <option value="{{ $produto->id }}">
                                                    {{ $produto->nome }}{{ $produto->sku ? ' - ' . $produto->sku : '' }}
                                                    | Estoque: {{ number_format((float) $produto->estoque_atual, 4, ',', '.') }} {{ $produto->unidade_padrao }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <input type="number" step="0.0001" wire:model="itensVenda.{{ $index }}.quantidade" placeholder="Quantidade" class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm">
                                    </div>
                                    <div class="sm:col-span-3">
                                        <input type="number" step="0.0001" wire:model="itensVenda.{{ $index }}.valor_unitario" placeholder="Valor unitario" class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm">
                                    </div>
                                    <div class="sm:col-span-1">
                                        <button type="button" wire:click="removeItemLinha({{ $index }})" class="w-full rounded-lg border border-[#F2C8C3] px-2 py-2 text-xs font-medium text-[#9A4030] hover:bg-[#FDECEA]">X</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @error('itensVenda') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-3 rounded-lg border border-[#DCCFB7] bg-[#FFFEFA] p-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Total da Venda</p>
                            <p class="mt-1 text-sm font-semibold text-[#1A3A0A]">R$ {{ number_format($this->valorTotalVendaCalculado(), 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Contas a Receber</p>
                            <p class="mt-1 text-sm font-semibold text-[#1A3A0A]">
                                @if($form['condicao_recebimento'] === 'parcelado')
                                    {{ (int) ($form['quantidade_parcelas'] ?: 0) }} parcelas
                                @elseif((bool) $form['conta_recebida_no_ato'])
                                    Venda marcada como recebida no ato
                                @else
                                    1 conta em aberto
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="closeFormModal" class="rounded-lg border border-[#DCCFB7] px-4 py-2 text-sm">Cancelar</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="salvarVenda" class="rounded-lg bg-[#2D5A1B] px-4 py-2 text-sm font-semibold text-white disabled:opacity-60">
                            <span wire:loading.remove wire:target="salvarVenda">Salvar Venda</span>
                            <span wire:loading.inline wire:target="salvarVenda">Salvando...</span>
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
