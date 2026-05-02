<div class="relative space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-[#1A3A0A]">Clientes</h2>
            <p class="text-sm text-[#8A7A60]">Cadastro e manutencao de clientes (PF e PJ).</p>
        </div>
        <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
            <div class="w-full sm:w-80">
                <label for="search" class="sr-only">Buscar cliente</label>
                <div class="relative">
                    <input
                        id="search"
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        wire:loading.attr="disabled"
                        wire:target="search"
                        placeholder="Buscar por nome, documento, email..."
                        class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2.5 pr-10 text-sm text-[#1A3A0A] placeholder-[#9A8B70] focus:border-[#2D5A1B] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/20 disabled:cursor-not-allowed disabled:bg-[#F6F1E7]"
                    >
                    <div wire:loading.flex wire:target="search" class="pointer-events-none absolute inset-y-0 right-3 items-center">
                        <svg class="h-4 w-4 animate-spin text-[#2D5A1B]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <button
                type="button"
                wire:click="openCreateModal"
                wire:loading.attr="disabled"
                wire:target="openCreateModal"
                class="inline-flex items-center justify-center rounded-lg bg-[#2D5A1B] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#234716] disabled:cursor-not-allowed disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="openCreateModal">Novo Cliente</span>
                <span wire:loading.inline wire:target="openCreateModal" class="items-center gap-2">
                    <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Abrindo...
                </span>
            </button>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-lg border border-[#B5D9A5] bg-[#ECF8E7] px-4 py-3 text-sm text-[#234A14]">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-5">
        <section class="rounded-xl border border-[#E0D8C8] bg-white p-5 shadow-sm">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-[#3A5C2A]">Lista de Clientes</h3>

            <div class="rounded-lg border border-[#EFE7D9] bg-[#FCFAF4] p-3 sm:p-4" wire:loading.class="opacity-60" wire:target="search,edit,delete,gotoPage,previousPage,nextPage,save">
                <div class="grid grid-cols-1 gap-3 xl:grid-cols-2">
                    @forelse ($clientes as $cliente)
                        <article class="rounded-xl border border-[#E8DECE] bg-white p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-[#1A3A0A]">{{ $cliente->nome }}</p>
                                    <p class="text-xs text-[#8A7A60]">{{ $cliente->tipo }}{{ $cliente->documento ? ' - ' . $cliente->documento : '' }}</p>
                                </div>
                                @if($cliente->ativo)
                                    <span class="inline-flex rounded-full bg-[#EAF4E2] px-2 py-0.5 text-xs font-medium text-[#2D5A1B]">Ativo</span>
                                @else
                                    <span class="inline-flex rounded-full bg-[#FDECEA] px-2 py-0.5 text-xs font-medium text-[#8A3A2A]">Inativo</span>
                                @endif
                            </div>

                            <dl class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <dt class="text-[11px] font-medium uppercase tracking-wide text-[#8A7A60]">Contato</dt>
                                    <dd class="mt-1 text-sm text-[#4F5D45]">{{ $cliente->telefone ?: '-' }}</dd>
                                    <dd class="text-xs text-[#8A7A60]">{{ $cliente->email ?: '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] font-medium uppercase tracking-wide text-[#8A7A60]">Cidade/UF</dt>
                                    <dd class="mt-1 text-sm text-[#4F5D45]">{{ ($cliente->cidade ?: '-') . ($cliente->uf ? '/' . $cliente->uf : '') }}</dd>
                                </div>
                            </dl>

                            <div class="mt-4 flex justify-end gap-2">
                                <button
                                    type="button"
                                    wire:click="edit({{ $cliente->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="edit({{ $cliente->id }})"
                                    class="rounded-md border border-[#DCCFB7] px-2.5 py-1.5 text-xs font-medium text-[#4F5D45] transition hover:bg-[#F7F3EA] disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    <span wire:loading.remove wire:target="edit({{ $cliente->id }})">Editar</span>
                                    <span wire:loading.inline wire:target="edit({{ $cliente->id }})">...</span>
                                </button>
                                <button
                                    type="button"
                                    wire:click="delete({{ $cliente->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="delete({{ $cliente->id }})"
                                    onclick="confirm('Deseja excluir este cliente?') || event.stopImmediatePropagation()"
                                    class="rounded-md border border-[#F2C8C3] px-2.5 py-1.5 text-xs font-medium text-[#9A4030] transition hover:bg-[#FDECEA] disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    <span wire:loading.remove wire:target="delete({{ $cliente->id }})">Excluir</span>
                                    <span wire:loading.inline wire:target="delete({{ $cliente->id }})">...</span>
                                </button>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-xl border border-dashed border-[#DCCFB7] bg-white px-4 py-8 text-center text-sm text-[#8A7A60]">
                            Nenhum cliente cadastrado.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4">
                {{ $clientes->links() }}
            </div>
        </section>
    </div>

    @if($showFormModal)
        <div class="fixed inset-0 z-40 bg-[#1A1A1A]/50" wire:click="cancelEdit"></div>

        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <section class="max-h-[95vh] w-full max-w-3xl overflow-y-auto rounded-xl border border-[#E0D8C8] bg-white p-5 shadow-xl">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-[#3A5C2A]">
                        {{ $editingId ? 'Editar Cliente' : 'Novo Cliente' }}
                    </h3>
                    <button
                        type="button"
                        wire:click="cancelEdit"
                        wire:loading.attr="disabled"
                        wire:target="cancelEdit"
                        class="rounded-md px-2 py-1 text-[#8A7A60] transition hover:bg-[#F7F3EA] hover:text-[#4F5D45] disabled:cursor-not-allowed disabled:opacity-60"
                        aria-label="Fechar modal"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4" wire:click.stop>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label for="tipo" class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Tipo</label>
                            <select
                                id="tipo"
                                wire:model="form.tipo"
                                class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm text-[#1A3A0A] focus:border-[#2D5A1B] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/20"
                            >
                                <option value="PJ">PJ</option>
                                <option value="PF">PF</option>
                            </select>
                            @error('form.tipo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="ativo" class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Status</label>
                            <select
                                id="ativo"
                                wire:model="form.ativo"
                                class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm text-[#1A3A0A] focus:border-[#2D5A1B] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/20"
                            >
                                <option value="1">Ativo</option>
                                <option value="0">Inativo</option>
                            </select>
                            @error('form.ativo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="nome" class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Nome</label>
                        <input
                            id="nome"
                            type="text"
                            wire:model="form.nome"
                            class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm text-[#1A3A0A] focus:border-[#2D5A1B] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/20"
                        >
                        @error('form.nome') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label for="documento" class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">CPF/CNPJ</label>
                            <input
                                id="documento"
                                type="text"
                                wire:model="form.documento"
                                class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm text-[#1A3A0A] focus:border-[#2D5A1B] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/20"
                            >
                            @error('form.documento') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        @if($form['tipo'] === 'PJ')
                            <div>
                                <label for="inscricao_estadual" class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Inscricao Estadual</label>
                                <input
                                    id="inscricao_estadual"
                                    type="text"
                                    wire:model="form.inscricao_estadual"
                                    class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm text-[#1A3A0A] focus:border-[#2D5A1B] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/20"
                                >
                                @error('form.inscricao_estadual') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @endif

                        <div>
                            <label for="telefone" class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Telefone</label>
                            <input
                                id="telefone"
                                type="text"
                                wire:model="form.telefone"
                                class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm text-[#1A3A0A] focus:border-[#2D5A1B] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/20"
                            >
                            @error('form.telefone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="email" class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Email</label>
                        <input
                            id="email"
                            type="email"
                            wire:model="form.email"
                            class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm text-[#1A3A0A] focus:border-[#2D5A1B] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/20"
                        >
                        @error('form.email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="sm:col-span-2">
                            <label for="cidade" class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Cidade</label>
                            <input
                                id="cidade"
                                type="text"
                                wire:model="form.cidade"
                                class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm text-[#1A3A0A] focus:border-[#2D5A1B] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/20"
                            >
                            @error('form.cidade') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="uf" class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">UF</label>
                            <input
                                id="uf"
                                type="text"
                                wire:model="form.uf"
                                maxlength="2"
                                class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm uppercase text-[#1A3A0A] focus:border-[#2D5A1B] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/20"
                            >
                            @error('form.uf') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="endereco" class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Endereco</label>
                        <input
                            id="endereco"
                            type="text"
                            wire:model="form.endereco"
                            class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm text-[#1A3A0A] focus:border-[#2D5A1B] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/20"
                        >
                        @error('form.endereco') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="observacoes" class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#6D7D63]">Observacoes</label>
                        <textarea
                            id="observacoes"
                            rows="3"
                            wire:model="form.observacoes"
                            class="w-full rounded-lg border border-[#DCCFB7] bg-white px-3 py-2 text-sm text-[#1A3A0A] focus:border-[#2D5A1B] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/20"
                        ></textarea>
                        @error('form.observacoes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-1">
                        <button
                            type="button"
                            wire:click="cancelEdit"
                            wire:loading.attr="disabled"
                            wire:target="cancelEdit,save"
                            class="inline-flex items-center rounded-lg border border-[#DCCFB7] px-4 py-2 text-sm font-medium text-[#6D7D63] transition hover:bg-[#F7F3EA] disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="inline-flex items-center rounded-lg bg-[#2D5A1B] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#234716] disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="save">
                                {{ $editingId ? 'Salvar Alteracoes' : 'Cadastrar Cliente' }}
                            </span>
                            <span wire:loading.inline wire:target="save" class="items-center gap-2">
                                <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Salvando...
                            </span>
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
