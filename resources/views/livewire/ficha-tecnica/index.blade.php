<div class="relative space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-[#1A3A0A]">Ficha Tecnica</h2>
            <p class="text-sm text-[#8A7A60]">Cadastre produtos e insumos. O consumo sera informado no lancamento da producao.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-lg border border-[#B5D9A5] bg-[#ECF8E7] px-4 py-3 text-sm text-[#234A14]">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
        <section class="rounded-xl border border-[#E0D8C8] bg-white p-5 shadow-sm">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-[#3A5C2A]">Produtos</h3>
                <button
                    type="button"
                    wire:click="openProdutoModal"
                    wire:loading.attr="disabled"
                    wire:target="openProdutoModal"
                    class="inline-flex items-center justify-center rounded-lg bg-[#2D5A1B] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#234716] disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="openProdutoModal">Novo Produto</span>
                    <span wire:loading.inline wire:target="openProdutoModal">Abrindo...</span>
                </button>
            </div>

            <div class="mb-4">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="searchProduto"
                    wire:loading.attr="disabled"
                    wire:target="searchProduto"
                    placeholder="Buscar produto por nome ou SKU"
                    class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm text-[#1A3A0A] focus:border-[#2D5A1B] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/20"
                >
            </div>

            <div class="rounded-lg border border-[#EFE7D9] bg-[#FCFAF4] p-3 sm:p-4" wire:loading.class="opacity-60" wire:target="searchProduto,editProduto,deleteProduto,gotoPage,previousPage,nextPage,saveProduto">
                <div class="grid grid-cols-1 gap-3">
                    @forelse ($produtos as $produto)
                        <article class="rounded-xl border border-[#E8DECE] bg-white p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-[#1A3A0A]">{{ $produto->nome }}</p>
                                    <p class="text-xs text-[#8A7A60]">{{ $produto->sku ?: 'Sem SKU' }}</p>
                                </div>
                                @if($produto->ativo)
                                    <span class="inline-flex rounded-full bg-[#EAF4E2] px-2 py-0.5 text-xs font-medium text-[#2D5A1B]">Ativo</span>
                                @else
                                    <span class="inline-flex rounded-full bg-[#FDECEA] px-2 py-0.5 text-xs font-medium text-[#8A3A2A]">Inativo</span>
                                @endif
                            </div>
                            <p class="mt-3 text-sm text-[#4F5D45]">Unidade: {{ $produto->unidade_padrao }}</p>
                            <div class="mt-4 flex justify-end gap-2">
                                <button type="button" wire:click="editProduto({{ $produto->id }})" class="rounded-md border border-[#DCCFB7] px-2.5 py-1.5 text-xs font-medium text-[#4F5D45] hover:bg-[#F7F3EA]">Editar</button>
                                <button type="button" wire:click="deleteProduto({{ $produto->id }})" onclick="confirm('Deseja excluir este produto?') || event.stopImmediatePropagation()" class="rounded-md border border-[#F2C8C3] px-2.5 py-1.5 text-xs font-medium text-[#9A4030] hover:bg-[#FDECEA]">Excluir</button>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-xl border border-dashed border-[#DCCFB7] bg-white px-4 py-8 text-center text-sm text-[#8A7A60]">Nenhum produto cadastrado.</div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4">{{ $produtos->links() }}</div>
        </section>

        <section class="rounded-xl border border-[#E0D8C8] bg-white p-5 shadow-sm">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-[#3A5C2A]">Insumos</h3>
                <button
                    type="button"
                    wire:click="openInsumoModal"
                    wire:loading.attr="disabled"
                    wire:target="openInsumoModal"
                    class="inline-flex items-center justify-center rounded-lg bg-[#2D5A1B] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#234716] disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="openInsumoModal">Novo Insumo</span>
                    <span wire:loading.inline wire:target="openInsumoModal">Abrindo...</span>
                </button>
            </div>

            <div class="mb-4">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="searchInsumo"
                    wire:loading.attr="disabled"
                    wire:target="searchInsumo"
                    placeholder="Buscar insumo por nome ou codigo"
                    class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm text-[#1A3A0A] focus:border-[#2D5A1B] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/20"
                >
            </div>

            <div class="rounded-lg border border-[#EFE7D9] bg-[#FCFAF4] p-3 sm:p-4" wire:loading.class="opacity-60" wire:target="searchInsumo,editInsumo,deleteInsumo,gotoPage,previousPage,nextPage,saveInsumo">
                <div class="grid grid-cols-1 gap-3">
                    @forelse ($insumos as $insumo)
                        <article class="rounded-xl border border-[#E8DECE] bg-white p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-[#1A3A0A]">{{ $insumo->nome }}</p>
                                    <p class="text-xs text-[#8A7A60]">{{ $insumo->codigo ?: 'Sem codigo' }}</p>
                                </div>
                                <span class="text-xs font-medium text-[#4F5D45]">{{ $insumo->unidade }}</span>
                            </div>
                            <p class="mt-3 text-sm text-[#4F5D45]">
                                @if((float) $insumo->custo_unitario > 0)
                                    R$ {{ number_format($insumo->custo_unitario, 4, ',', '.') }}
                                @else
                                    <span class="text-[#8A7A60]">A calcular</span>
                                @endif
                            </p>
                            <div class="mt-4 flex justify-end gap-2">
                                <button type="button" wire:click="editInsumo({{ $insumo->id }})" class="rounded-md border border-[#DCCFB7] px-2.5 py-1.5 text-xs font-medium text-[#4F5D45] hover:bg-[#F7F3EA]">Editar</button>
                                <button type="button" wire:click="deleteInsumo({{ $insumo->id }})" onclick="confirm('Deseja excluir este insumo?') || event.stopImmediatePropagation()" class="rounded-md border border-[#F2C8C3] px-2.5 py-1.5 text-xs font-medium text-[#9A4030] hover:bg-[#FDECEA]">Excluir</button>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-xl border border-dashed border-[#DCCFB7] bg-white px-4 py-8 text-center text-sm text-[#8A7A60]">Nenhum insumo cadastrado.</div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4">{{ $insumos->links() }}</div>
        </section>
    </div>

    @if($showProdutoModal)
        <div class="fixed inset-0 z-40 bg-[#1A1A1A]/50" wire:click="cancelProduto"></div>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <section class="w-full max-w-2xl rounded-xl border border-[#E0D8C8] bg-white p-5 shadow-xl">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-[#3A5C2A]">{{ $editingProdutoId ? 'Editar Produto' : 'Novo Produto' }}</h3>
                <form wire:submit="saveProduto" class="space-y-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div><label class="mb-1 block text-xs text-[#6D7D63]">Nome</label><input type="text" wire:model="produtoForm.nome" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm"></div>
                        <div><label class="mb-1 block text-xs text-[#6D7D63]">SKU</label><input type="text" wire:model="produtoForm.sku" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm"></div>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div><label class="mb-1 block text-xs text-[#6D7D63]">Unidade Padrao</label><input type="text" wire:model="produtoForm.unidade_padrao" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm"></div>
                        <div><label class="mb-1 block text-xs text-[#6D7D63]">Status</label><select wire:model="produtoForm.ativo" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm"><option value="1">Ativo</option><option value="0">Inativo</option></select></div>
                    </div>
                    <div><label class="mb-1 block text-xs text-[#6D7D63]">Observacoes</label><textarea rows="3" wire:model="produtoForm.observacoes" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm"></textarea></div>
                    <div class="flex justify-end gap-2"><button type="button" wire:click="cancelProduto" class="rounded-lg border border-[#DCCFB7] px-4 py-2 text-sm">Cancelar</button><button type="submit" class="rounded-lg bg-[#2D5A1B] px-4 py-2 text-sm font-semibold text-white">Salvar Produto</button></div>
                </form>
            </section>
        </div>
    @endif

    @if($showInsumoModal)
        <div class="fixed inset-0 z-40 bg-[#1A1A1A]/50" wire:click="cancelInsumo"></div>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <section class="w-full max-w-2xl rounded-xl border border-[#E0D8C8] bg-white p-5 shadow-xl">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-[#3A5C2A]">{{ $editingInsumoId ? 'Editar Insumo' : 'Novo Insumo' }}</h3>
                <form wire:submit="saveInsumo" class="space-y-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div><label class="mb-1 block text-xs text-[#6D7D63]">Nome</label><input type="text" wire:model="insumoForm.nome" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm"></div>
                        <div><label class="mb-1 block text-xs text-[#6D7D63]">Codigo</label><input type="text" wire:model="insumoForm.codigo" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm"></div>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div><label class="mb-1 block text-xs text-[#6D7D63]">Unidade</label><input type="text" wire:model="insumoForm.unidade" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm"></div>
                        <div class="rounded-lg border border-[#E7DFCF] bg-[#FAF7F0] px-3 py-2">
                            <p class="text-xs font-medium text-[#6D7D63]">Custo Unitario</p>
                            <p class="text-sm text-[#8A7A60]">Calculado automaticamente pelas entradas de insumo</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div><label class="mb-1 block text-xs text-[#6D7D63]">Status</label><select wire:model="insumoForm.ativo" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm"><option value="1">Ativo</option><option value="0">Inativo</option></select></div>
                    </div>
                    <div><label class="mb-1 block text-xs text-[#6D7D63]">Observacoes</label><textarea rows="3" wire:model="insumoForm.observacoes" class="w-full rounded-lg border border-[#DCCFB7] px-3 py-2 text-sm"></textarea></div>
                    <div class="flex justify-end gap-2"><button type="button" wire:click="cancelInsumo" class="rounded-lg border border-[#DCCFB7] px-4 py-2 text-sm">Cancelar</button><button type="submit" class="rounded-lg bg-[#2D5A1B] px-4 py-2 text-sm font-semibold text-white">Salvar Insumo</button></div>
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
