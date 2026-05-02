<div class="relative space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-[#1A3A0A]">Estoque</h2>
            <p class="text-sm text-[#8A7A60]">Produtos com estoque positivo e lancamento de producao.</p>
        </div>

        <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
            <div class="w-full sm:w-80">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    wire:loading.attr="disabled"
                    wire:target="search"
                    placeholder="Buscar produto por nome ou SKU"
                    class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2.5 text-sm text-[#1A3A0A] focus:border-[#2D5A1B] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/20"
                >
            </div>

            <button
                type="button"
                wire:click="openProducaoModal"
                wire:loading.attr="disabled"
                wire:target="openProducaoModal"
                class="inline-flex items-center justify-center rounded-lg bg-[#2D5A1B] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#234716] disabled:cursor-not-allowed disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="openProducaoModal">Adicionar Producao</span>
                <span wire:loading.inline wire:target="openProducaoModal">Abrindo...</span>
            </button>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-lg border border-[#B5D9A5] bg-[#ECF8E7] px-4 py-3 text-sm text-[#234A14]">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-lg border border-[#F2C8C3] bg-[#FDECEA] px-4 py-3 text-sm text-[#9A4030]">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <section class="xl:col-span-2 rounded-xl border border-[#E0D8C8] bg-white p-5 shadow-sm">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-[#3A5C2A]">Produtos com Estoque Positivo</h3>

            <div class="rounded-lg border border-[#EFE7D9] bg-[#FCFAF4] p-3 sm:p-4" wire:loading.class="opacity-60" wire:target="search,gotoPage,previousPage,nextPage,salvarProducao">
                <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                    @forelse ($estoques as $produto)
                        <article class="rounded-xl border border-[#E8DECE] bg-white p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-[#1A3A0A]">{{ $produto->nome }}</p>
                                    <p class="text-xs text-[#8A7A60]">{{ $produto->sku ?: 'Sem SKU' }}</p>
                                </div>
                                <span class="inline-flex rounded-full bg-[#EAF4E2] px-2 py-0.5 text-xs font-medium text-[#2D5A1B]">Em estoque</span>
                            </div>
                            <div class="mt-4 rounded-lg bg-[#FAF7F0] px-3 py-2">
                                <p class="text-[11px] font-medium uppercase tracking-wide text-[#8A7A60]">Saldo atual</p>
                                <p class="mt-1 text-sm font-semibold text-[#1A3A0A]">{{ number_format((float) $produto->estoque_atual, 4, ',', '.') }} {{ $produto->unidade_padrao }}</p>
                            </div>
                            <div class="mt-3 flex justify-end">
                                <button
                                    type="button"
                                    wire:click="openHistoricoModal({{ $produto->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="openHistoricoModal({{ $produto->id }})"
                                    class="rounded-md border border-[#DCCFB7] px-3 py-1.5 text-xs font-medium text-[#4F5D45] transition hover:bg-[#F7F3EA] disabled:opacity-60"
                                >
                                    <span wire:loading.remove wire:target="openHistoricoModal({{ $produto->id }})">Histórico de Produções</span>
                                    <span wire:loading.inline wire:target="openHistoricoModal({{ $produto->id }})">...</span>
                                </button>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-xl border border-dashed border-[#DCCFB7] bg-white px-4 py-8 text-center text-sm text-[#8A7A60]">
                            Nenhum produto com estoque positivo.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4">{{ $estoques->links() }}</div>
        </section>

        <section class="rounded-xl border border-[#E0D8C8] bg-white p-5 shadow-sm">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-[#3A5C2A]">Ultimas Producoes</h3>
            <div class="space-y-2">
                @forelse($ultimasProducoes as $producao)
                    <div class="rounded-lg border border-[#EFE7D9] bg-[#FAF7F0] p-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-semibold text-[#1A3A0A]">{{ \Carbon\Carbon::parse($producao->data_producao)->format('d/m/Y H:i') }}</p>
                                <p class="text-xs text-[#8A7A60] mt-1">Total consumido: {{ number_format((float) $producao->total_insumos_consumidos, 4, ',', '.') }}</p>
                            </div>
                            <div class="flex shrink-0 flex-col gap-1">
                                <button
                                    type="button"
                                    wire:click="openProducaoDetailsModal({{ $producao->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="openProducaoDetailsModal({{ $producao->id }})"
                                    class="rounded-md border border-[#DCCFB7] px-2.5 py-1 text-xs font-medium text-[#4F5D45] transition hover:bg-[#F7F3EA] disabled:opacity-60"
                                >
                                    <span wire:loading.remove wire:target="openProducaoDetailsModal({{ $producao->id }})">Detalhes</span>
                                    <span wire:loading.inline wire:target="openProducaoDetailsModal({{ $producao->id }})">...</span>
                                </button>
                                <button
                                    type="button"
                                    wire:click="promptExcluirProducao({{ $producao->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="promptExcluirProducao({{ $producao->id }})"
                                    class="rounded-md border border-[#F2C8C3] px-2.5 py-1 text-xs font-medium text-[#9A4030] transition hover:bg-[#FDECEA] disabled:opacity-60"
                                >
                                    <span wire:loading.remove wire:target="promptExcluirProducao({{ $producao->id }})">Excluir</span>
                                    <span wire:loading.inline wire:target="promptExcluirProducao({{ $producao->id }})">...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-[#8A7A60]">Nenhuma producao registrada.</p>
                @endforelse
            </div>
        </section>
    </div>

    @if($showProducaoModal)
        <div class="fixed inset-0 z-40 bg-[#1A1A1A]/50" wire:click="closeProducaoModal"></div>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <section class="max-h-[95vh] w-full max-w-4xl overflow-y-auto rounded-xl border border-[#E0D8C8] bg-white p-5 shadow-xl">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-[#3A5C2A]">Adicionar Producao</h3>
                    <button type="button" wire:click="closeProducaoModal" class="rounded-md px-2 py-1 text-[#8A7A60] hover:bg-[#F7F3EA]">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="salvarProducao" class="space-y-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Data da Producao</label>
                            <input type="datetime-local" wire:model="producaoForm.data_producao" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm">
                            @error('producaoForm.data_producao') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                        <div class="grid grid-cols-1 gap-3">

                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Observacoes</label>
                        <textarea rows="2" wire:model="producaoForm.observacoes" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm"></textarea>
                        @error('producaoForm.observacoes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="rounded-lg border border-[#EFE7D9] bg-[#FAF7F0] p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-sm font-semibold text-[#1A3A0A]">Produtos Produzidos</p>
                            <button type="button" wire:click="addItemLinha" class="rounded-md border border-[#DCCFB7] px-2.5 py-1.5 text-xs font-medium text-[#4F5D45] hover:bg-[#F7F3EA]">Adicionar Linha</button>
                        </div>

                        <div class="space-y-2">
                            @foreach($itens as $index => $item)
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-12">
                                    <div class="sm:col-span-7">
                                        <select wire:model="itens.{{ $index }}.produto_id" class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm">
                                            <option value="">Selecione o produto...</option>
                                            @foreach($produtosAtivos as $produto)
                                                <option value="{{ $produto->id }}">{{ $produto->nome }}{{ $produto->sku ? ' - ' . $produto->sku : '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="sm:col-span-4">
                                        <input type="number" step="0.0001" wire:model="itens.{{ $index }}.quantidade_produzida" placeholder="Quantidade" class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm">
                                    </div>
                                    <div class="sm:col-span-1">
                                        <button type="button" wire:click="removeItemLinha({{ $index }})" class="w-full rounded-lg border border-[#F2C8C3] px-2 py-2 text-xs font-medium text-[#9A4030] hover:bg-[#FDECEA]">X</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @error('itens') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="rounded-lg border border-[#EFE7D9] bg-[#FAF7F0] p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-sm font-semibold text-[#1A3A0A]">Quantidade Total em KG Produzida</p>
                            <button type="button" wire:click="addInsumoLinha" class="rounded-md border border-[#DCCFB7] px-2.5 py-1.5 text-xs font-medium text-[#4F5D45] hover:bg-[#F7F3EA]">Adicionar Linha</button>
                        </div>

                        <div class="space-y-2">
                            @foreach($insumosConsumidos as $index => $insumoItem)
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-12">
                                    <div class="sm:col-span-6">
                                        <select wire:model="insumosConsumidos.{{ $index }}.insumo_id" class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm">
                                            <option value="">Selecione o insumo...</option>
                                            @foreach($insumosAtivos as $insumo)
                                                <option value="{{ $insumo->id }}">{{ $insumo->nome }}{{ $insumo->codigo ? ' - ' . $insumo->codigo : '' }} | R$ {{ number_format((float) $insumo->custo_unitario, 4, ',', '.') }}/{{ $insumo->unidade }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="sm:col-span-4">
                                        <input type="number" step="0.0001" wire:model="insumosConsumidos.{{ $index }}.quantidade_consumida" placeholder="Quantidade consumida" class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <button type="button" wire:click="removeInsumoLinha({{ $index }})" class="w-full rounded-lg border border-[#F2C8C3] px-2 py-2 text-xs font-medium text-[#9A4030] hover:bg-[#FDECEA]">Remover</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @error('insumosConsumidos') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-3 rounded-lg border border-[#DCCFB7] bg-[#FFFEFA] p-4 sm:grid-cols-3">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Custo Total dos Insumos</p>
                            <p class="mt-1 text-sm font-semibold text-[#1A3A0A]">R$ {{ number_format($this->custoTotalInsumosCalculado(), 4, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Quantidade Total Produzida</p>
                            <p class="mt-1 text-sm font-semibold text-[#1A3A0A]">{{ number_format($this->quantidadeTotalProduzidaCalculada(), 4, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Custo Unitario Calculado</p>
                            <p class="mt-1 text-sm font-semibold text-[#1A3A0A]">R$ {{ number_format($this->custoUnitarioProduzidoCalculado(), 4, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="closeProducaoModal" class="rounded-lg border border-[#DCCFB7] px-4 py-2 text-sm">Cancelar</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="salvarProducao" class="rounded-lg bg-[#2D5A1B] px-4 py-2 text-sm font-semibold text-white disabled:opacity-60">
                            <span wire:loading.remove wire:target="salvarProducao">Salvar Producao</span>
                            <span wire:loading.inline wire:target="salvarProducao">Salvando...</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    @endif

    @if($showProducaoDetailsModal && $producaoDetalhes)
        <div class="fixed inset-0 z-40 bg-[#1A1A1A]/50" wire:click="closeProducaoDetailsModal"></div>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <section class="max-h-[95vh] w-full max-w-4xl overflow-y-auto rounded-xl border border-[#E0D8C8] bg-white p-5 shadow-xl" wire:click.stop>
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-[#3A5C2A]">Detalhes da Produção #{{ $producaoDetalhes->id }}</h3>
                        <p class="mt-1 text-xs text-[#8A7A60]">{{ $producaoDetalhes->data_producao?->format('d/m/Y H:i') }}</p>
                    </div>
                    <button type="button" wire:click="closeProducaoDetailsModal" class="rounded-md px-2 py-1 text-[#8A7A60] hover:bg-[#F7F3EA]">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="rounded-lg border border-[#EFE7D9] bg-[#FCFAF4] p-3">
                            <p class="text-[11px] font-medium uppercase tracking-wide text-[#8A7A60]">Total Insumos Consumidos</p>
                            <p class="mt-1 text-sm font-semibold text-[#1A3A0A]">{{ number_format((float) $producaoDetalhes->total_insumos_consumidos, 4, ',', '.') }}</p>
                        </div>
                        <div class="rounded-lg border border-[#EFE7D9] bg-[#FCFAF4] p-3">
                            <p class="text-[11px] font-medium uppercase tracking-wide text-[#8A7A60]">Custo Total</p>
                            <p class="mt-1 text-sm font-semibold text-[#1A3A0A]">R$ {{ number_format((float) $producaoDetalhes->insumos->sum('custo_total'), 2, ',', '.') }}</p>
                        </div>
                        <div class="rounded-lg border border-[#EFE7D9] bg-[#FCFAF4] p-3">
                            <p class="text-[11px] font-medium uppercase tracking-wide text-[#8A7A60]">Produtos Produzidos</p>
                            <p class="mt-1 text-sm font-semibold text-[#1A3A0A]">{{ $producaoDetalhes->itens->count() }}</p>
                        </div>
                    </div>

                    @if($producaoDetalhes->observacoes)
                        <div class="rounded-lg border border-[#EFE7D9] bg-[#FFFEFA] p-3">
                            <p class="text-[11px] font-medium uppercase tracking-wide text-[#8A7A60]">Observações</p>
                            <p class="mt-1 text-sm text-[#4F5D45]">{{ $producaoDetalhes->observacoes }}</p>
                        </div>
                    @endif

                    <div class="rounded-lg border border-[#EFE7D9] bg-[#FAF7F0] p-4">
                        <p class="mb-3 text-sm font-semibold text-[#1A3A0A]">Produtos Produzidos</p>
                        <div class="space-y-2">
                            @forelse($producaoDetalhes->itens as $item)
                                <div class="grid grid-cols-1 gap-2 rounded-lg border border-[#E8DECE] bg-white p-3 sm:grid-cols-3">
                                    <div>
                                        <p class="text-[11px] font-medium uppercase tracking-wide text-[#8A7A60]">Produto</p>
                                        <p class="mt-1 text-sm font-medium text-[#1A3A0A]">{{ $item->produto?->nome ?: 'Produto removido' }}</p>
                                        <p class="text-xs text-[#8A7A60]">{{ $item->produto?->sku ?: '' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-medium uppercase tracking-wide text-[#8A7A60]">Quantidade</p>
                                        <p class="mt-1 text-sm text-[#4F5D45]">{{ number_format((float) $item->quantidade_produzida, 4, ',', '.') }} {{ $item->unidade }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-medium uppercase tracking-wide text-[#8A7A60]">Custo Unitário</p>
                                        <p class="mt-1 text-sm text-[#4F5D45]">R$ {{ number_format($producaoDetalhes->itens->count() > 0 && (float)$producaoDetalhes->insumos->sum('custo_total') > 0 && $producaoDetalhes->itens->sum('quantidade_produzida') > 0 ? (float)$producaoDetalhes->insumos->sum('custo_total') / (float)$producaoDetalhes->itens->sum('quantidade_produzida') : 0, 4, ',', '.') }}/{{ $item->unidade }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-[#8A7A60]">Nenhum item registrado.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-lg border border-[#EFE7D9] bg-[#FAF7F0] p-4">
                        <p class="mb-3 text-sm font-semibold text-[#1A3A0A]">Insumos Consumidos</p>
                        <div class="space-y-2">
                            @forelse($producaoDetalhes->insumos as $insumo)
                                <div class="grid grid-cols-1 gap-2 rounded-lg border border-[#E8DECE] bg-white p-3 sm:grid-cols-4">
                                    <div>
                                        <p class="text-[11px] font-medium uppercase tracking-wide text-[#8A7A60]">Insumo</p>
                                        <p class="mt-1 text-sm font-medium text-[#1A3A0A]">{{ $insumo->insumo?->nome ?: 'Insumo removido' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-medium uppercase tracking-wide text-[#8A7A60]">Quantidade</p>
                                        <p class="mt-1 text-sm text-[#4F5D45]">{{ number_format((float) $insumo->quantidade_consumida, 4, ',', '.') }} {{ $insumo->unidade }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-medium uppercase tracking-wide text-[#8A7A60]">Custo Unitário</p>
                                        <p class="mt-1 text-sm text-[#4F5D45]">R$ {{ number_format((float) $insumo->custo_unitario, 4, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-medium uppercase tracking-wide text-[#8A7A60]">Custo Total</p>
                                        <p class="mt-1 text-sm font-semibold text-[#1A3A0A]">R$ {{ number_format((float) $insumo->custo_total, 2, ',', '.') }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-[#8A7A60]">Nenhum insumo registrado.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            wire:click="promptExcluirProducao({{ $producaoDetalhes->id }})"
                            wire:loading.attr="disabled"
                            wire:target="promptExcluirProducao({{ $producaoDetalhes->id }})"
                            class="rounded-lg border border-[#F2C8C3] px-4 py-2 text-sm font-medium text-[#9A4030] hover:bg-[#FDECEA] disabled:opacity-60"
                        >Excluir Produção</button>
                        <button type="button" wire:click="closeProducaoDetailsModal" class="rounded-lg border border-[#DCCFB7] px-4 py-2 text-sm">Fechar</button>
                    </div>
                </div>
            </section>
        </div>
    @endif

    @if($showHistoricoModal && $historicoProduto)
        <div class="fixed inset-0 z-40 bg-[#1A1A1A]/50" wire:click="closeHistoricoModal"></div>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <section class="max-h-[95vh] w-full max-w-4xl overflow-y-auto rounded-xl border border-[#E0D8C8] bg-white p-5 shadow-xl" wire:click.stop>
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-[#3A5C2A]">Histórico de Produções</h3>
                        <p class="mt-1 text-xs text-[#8A7A60]">{{ $historicoProduto->nome }}{{ $historicoProduto->sku ? ' — ' . $historicoProduto->sku : '' }}</p>
                    </div>
                    <button type="button" wire:click="closeHistoricoModal" class="rounded-md px-2 py-1 text-[#8A7A60] hover:bg-[#F7F3EA]">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-3">
                    @forelse($historicoProducoes as $producao)
                        @php
                            $itemProduto = $producao->itens->first();
                            $custoTotal  = (float) $producao->insumos->sum('custo_total');
                            $qtdTotal    = (float) $producao->itens->sum('quantidade_produzida');
                            $custoUnit   = $qtdTotal > 0 ? $custoTotal / $qtdTotal : 0;
                        @endphp
                        <div class="rounded-xl border border-[#E8DECE] bg-white p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-[#1A3A0A]">Produção #{{ $producao->id }}</p>
                                    <p class="text-xs text-[#8A7A60]">{{ $producao->data_producao?->format('d/m/Y H:i') }}</p>
                                </div>
                                <button
                                    type="button"
                                    wire:click="openProducaoDetailsModal({{ $producao->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="openProducaoDetailsModal({{ $producao->id }})"
                                    class="shrink-0 rounded-md border border-[#DCCFB7] px-2.5 py-1 text-xs font-medium text-[#4F5D45] transition hover:bg-[#F7F3EA] disabled:opacity-60"
                                >
                                    <span wire:loading.remove wire:target="openProducaoDetailsModal({{ $producao->id }})">Ver detalhes</span>
                                    <span wire:loading.inline wire:target="openProducaoDetailsModal({{ $producao->id }})">...</span>
                                </button>
                            </div>
                            <dl class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div>
                                    <dt class="text-[11px] font-medium uppercase tracking-wide text-[#8A7A60]">Quantidade Produzida</dt>
                                    <dd class="mt-1 text-sm font-semibold text-[#1A3A0A]">{{ number_format((float) $itemProduto?->quantidade_produzida, 4, ',', '.') }} {{ $itemProduto?->unidade }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] font-medium uppercase tracking-wide text-[#8A7A60]">Custo Total</dt>
                                    <dd class="mt-1 text-sm font-semibold text-[#1A3A0A]">R$ {{ number_format($custoTotal, 2, ',', '.') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] font-medium uppercase tracking-wide text-[#8A7A60]">Custo Unitário</dt>
                                    <dd class="mt-1 text-sm font-semibold text-[#1A3A0A]">R$ {{ number_format($custoUnit, 4, ',', '.') }}/{{ $itemProduto?->unidade }}</dd>
                                </div>
                            </dl>
                            @if($producao->observacoes)
                                <p class="mt-2 text-xs text-[#8A7A60]">{{ $producao->observacoes }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-[#DCCFB7] bg-white px-4 py-8 text-center text-sm text-[#8A7A60]">
                            Nenhuma produção registrada para este produto.
                        </div>
                    @endforelse
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="button" wire:click="closeHistoricoModal" class="rounded-lg border border-[#DCCFB7] px-4 py-2 text-sm">Fechar</button>
                </div>
            </section>
        </div>
    @endif

    @if($showExcluirProducaoConfirm && $excluirProducaoId)
        <div class="fixed inset-0 z-[55] bg-[#1A1A1A]/60" wire:click="cancelarExcluirPrompt"></div>
        <div class="fixed inset-0 z-[56] flex items-center justify-center p-4">
            <div class="w-full max-w-md rounded-xl border border-[#E0D8C8] bg-white p-6 shadow-xl" wire:click.stop>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-[#9A4030]">Excluir Produção #{{ $excluirProducaoId }}</h3>
                <p class="mt-2 text-sm text-[#4F5D45]">Esta ação é <strong>irreversível</strong>. O sistema irá:</p>
                <ul class="mt-1 list-inside list-disc space-y-1 text-sm text-[#4F5D45]">
                    <li>Subtrair a quantidade produzida do estoque de cada produto</li>
                    <li>Devolver os insumos consumidos ao estoque (via lote de correção)</li>
                    <li>Apagar todos os registros desta produção</li>
                </ul>
                <p class="mt-3 text-sm font-medium text-[#9A4030]">Atenção: a exclusão será bloqueada se algum produto já teve parte do estoque vendido.</p>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" wire:click="cancelarExcluirPrompt" class="rounded-lg border border-[#DCCFB7] px-4 py-2 text-sm">Voltar</button>
                    <button
                        type="button"
                        wire:click="excluirProducao"
                        wire:loading.attr="disabled"
                        wire:target="excluirProducao"
                        class="rounded-lg bg-[#9A4030] px-4 py-2 text-sm font-semibold text-white hover:bg-[#7D3328] disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="excluirProducao">Excluir Definitivamente</span>
                        <span wire:loading.inline wire:target="excluirProducao">Excluindo...</span>
                    </button>
                </div>
            </div>
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
