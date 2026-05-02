<?php

namespace App\Livewire\Compras;

use App\Models\Compra;
use App\Models\ContaPagar;
use App\Models\Fornecedor;
use App\Models\Insumo;
use App\Models\InsumoLote;
use App\Services\InsumoFifoService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public bool $showFormModal = false;
    public bool $showDetailsModal = false;
    public ?int $detailsCompraId = null;
    public bool $showEditModal = false;
    public ?int $editingCompraId = null;
    public bool $editCanEditFinancial = false;
    public string $search = '';

    public array $editForm = [
        'tipo'               => '',
        'data_compra'        => '',
        'fornecedor_id'      => '',
        'observacoes'        => '',
        'categoria_despesa'  => '',
        'descricao_despesa'  => '',
        'valor_despesa'      => '',
    ];

    public const CATEGORIAS_DESPESA = [
        'manutencao_carro'     => 'Manutenção de Carro',
        'combustivel'          => 'Combustível',
        'salario_funcionario'  => 'Salário de Funcionário',
        'gas_defumar'          => 'Gás para Defumar',
        'utensilios_frigorifico' => 'Compra de Utensílios para o Frigorífico',
    ];

    public array $form = [
        'tipo' => 'compra',
        'data_compra' => '',
        'fornecedor_id' => '',
        'condicao_pagamento' => 'a_vista',
        'conta_paga_no_ato' => false,
        'data_primeiro_vencimento' => '',
        'quantidade_parcelas' => 2,
        'intervalo_parcelas_dias' => 30,
        'observacoes' => '',
        // campos exclusivos de despesa
        'categoria_despesa' => '',
        'descricao_despesa' => '',
        'valor_despesa' => '',
    ];

    public array $itensCompra = [
        ['insumo_id' => '', 'quantidade' => '', 'valor_unitario' => ''],
    ];

    public function mount(): void
    {
        $this->resetForm();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function openDetailsModal(int $compraId): void
    {
        $this->detailsCompraId = $compraId;
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal(): void
    {
        $this->showDetailsModal = false;
        $this->detailsCompraId = null;
    }

    public function openEditModal(int $id): void
    {
        $compra = Compra::query()->with('contasPagar')->findOrFail($id);

        $allPendente = $compra->contasPagar->count() === 0
            || $compra->contasPagar->every(fn ($c) => $c->status === 'pendente');

        $this->editCanEditFinancial = $compra->tipo === 'despesa' && $allPendente;

        $descricaoDespesa = '';
        if ($compra->tipo === 'despesa' && $compra->contasPagar->isNotEmpty()) {
            $desc = $compra->contasPagar->first()->descricao;
            $desc = preg_replace('/ - Parcela \d+\/\d+$/', '', $desc);
            $categoryName = self::CATEGORIAS_DESPESA[$compra->categoria_despesa] ?? '';
            if ($categoryName !== '' && str_starts_with($desc, $categoryName . ': ')) {
                $desc = substr($desc, strlen($categoryName) + 2);
            }
            $descricaoDespesa = $desc;
        }

        $this->editForm = [
            'tipo'              => $compra->tipo,
            'data_compra'       => $compra->data_compra?->format('Y-m-d\TH:i') ?? '',
            'fornecedor_id'     => $compra->fornecedor_id ?? '',
            'observacoes'       => $compra->observacoes ?? '',
            'categoria_despesa' => $compra->categoria_despesa ?? '',
            'descricao_despesa' => $descricaoDespesa,
            'valor_despesa'     => number_format((float) $compra->valor_total, 2, '.', ''),
        ];

        $this->editingCompraId = $id;
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->editingCompraId = null;
        $this->editCanEditFinancial = false;
        $this->editForm = [
            'tipo'              => '',
            'data_compra'       => '',
            'fornecedor_id'     => '',
            'observacoes'       => '',
            'categoria_despesa' => '',
            'descricao_despesa' => '',
            'valor_despesa'     => '',
        ];
        $this->resetValidation();
    }

    public function saveEditCompra(): void
    {
        $compra = Compra::query()->with('contasPagar')->findOrFail($this->editingCompraId);

        $allPendente = $compra->contasPagar->count() === 0
            || $compra->contasPagar->every(fn ($c) => $c->status === 'pendente');
        $canEditFinancial = $compra->tipo === 'despesa' && $allPendente;

        $rules = [
            'editForm.data_compra'   => ['required', 'date'],
            'editForm.fornecedor_id' => ['nullable', 'integer', 'exists:fornecedores,id'],
            'editForm.observacoes'   => ['nullable', 'string', 'max:2000'],
        ];

        if ($canEditFinancial) {
            $rules['editForm.categoria_despesa'] = ['required', 'string', Rule::in(array_keys(self::CATEGORIAS_DESPESA))];
            $rules['editForm.descricao_despesa'] = ['required', 'string', 'max:500'];
            $rules['editForm.valor_despesa']     = ['required', 'numeric', 'gt:0'];
        }

        $this->validate($rules);

        $fornecedorId = ($this->editForm['fornecedor_id'] !== '' && $this->editForm['fornecedor_id'] !== null)
            ? (int) $this->editForm['fornecedor_id']
            : null;

        DB::transaction(function () use ($compra, $canEditFinancial, $fornecedorId) {
            $compra->update([
                'data_compra'   => $this->editForm['data_compra'],
                'fornecedor_id' => $fornecedorId,
                'observacoes'   => $this->editForm['observacoes'] !== '' ? $this->editForm['observacoes'] : null,
            ]);

            if ($canEditFinancial) {
                $newValor = (float) $this->editForm['valor_despesa'];
                $compra->update([
                    'categoria_despesa' => $this->editForm['categoria_despesa'],
                    'valor_total'       => $newValor,
                ]);

                $newCategoryName = self::CATEGORIAS_DESPESA[$this->editForm['categoria_despesa']];
                $newDescBase     = $newCategoryName . ': ' . $this->editForm['descricao_despesa'];
                $parcelas        = $compra->contasPagar->count();

                if ($parcelas > 0) {
                    $valorBase = round($newValor / $parcelas, 4);
                    $acumulado = 0.0;

                    foreach ($compra->contasPagar->sortBy('data_vencimento')->values() as $i => $conta) {
                        $num        = $i + 1;
                        $valorParc  = $num < $parcelas ? $valorBase : round($newValor - $acumulado, 4);
                        $acumulado += $valorParc;
                        $newDesc    = $parcelas > 1 ? "{$newDescBase} - Parcela {$num}/{$parcelas}" : $newDescBase;
                        $conta->update([
                            'descricao'      => $newDesc,
                            'valor_original' => $valorParc,
                            'valor_aberto'   => $valorParc,
                            'fornecedor_id'  => $fornecedorId,
                        ]);
                    }
                }
            } else {
                $compra->contasPagar->each(fn ($c) => $c->update(['fornecedor_id' => $fornecedorId]));
            }
        });

        session()->flash('status', 'Compra atualizada com sucesso.');
        $this->closeEditModal();
        $this->resetPage();
    }

    public function deleteCompra(int $id): void
    {
        $compra = Compra::query()->with(['contasPagar', 'itens'])->findOrFail($id);

        if ($compra->contasPagar->where('status', 'pago')->isNotEmpty()) {
            session()->flash('error', 'Nao e possivel excluir: esta compra possui contas ja pagas.');
            return;
        }

        if ($compra->tipo === 'compra') {
            $compraItemIds = $compra->itens->pluck('id');
            $consumido = InsumoLote::query()
                ->whereIn('compra_item_id', $compraItemIds)
                ->whereColumn('quantidade_saldo', '<', 'quantidade_entrada')
                ->exists();

            if ($consumido) {
                session()->flash('error', 'Nao e possivel excluir: estoque desta compra ja foi parcialmente consumido em producoes.');
                return;
            }
        }

        DB::transaction(function () use ($compra) {
            $compra->contasPagar()->delete();

            if ($compra->tipo === 'compra') {
                $insumoIds = $compra->itens->pluck('insumo_id')->unique()->values();

                foreach ($compra->itens as $item) {
                    InsumoLote::query()->where('compra_item_id', $item->id)->delete();
                }

                $compra->itens()->delete();

                $fifoService = app(InsumoFifoService::class);
                foreach ($insumoIds as $insumoId) {
                    $insumo = Insumo::query()->find($insumoId);
                    if ($insumo) {
                        $fifoService->recalcularInsumo($insumo);
                    }
                }
            }

            $compra->delete();
        });

        if ($this->detailsCompraId === $id) {
            $this->closeDetailsModal();
        }

        if ($this->editingCompraId === $id) {
            $this->closeEditModal();
        }

        session()->flash('status', 'Compra excluida com sucesso.');
        $this->resetPage();
    }

    public function addItemLinha(): void
    {
        $this->itensCompra[] = ['insumo_id' => '', 'quantidade' => '', 'valor_unitario' => ''];
    }

    public function removeItemLinha(int $index): void
    {
        if (count($this->itensCompra) <= 1) {
            return;
        }

        unset($this->itensCompra[$index]);
        $this->itensCompra = array_values($this->itensCompra);
    }

    public function valorTotalCompraCalculado(): float
    {
        return (float) collect($this->itensCompra)
            ->filter(function (array $item) {
                return ($item['insumo_id'] ?? '') !== ''
                    && ($item['quantidade'] ?? '') !== ''
                    && ($item['valor_unitario'] ?? '') !== ''
                    && (float) $item['quantidade'] > 0
                    && (float) $item['valor_unitario'] >= 0;
            })
            ->sum(function (array $item) {
                return (float) $item['quantidade'] * (float) $item['valor_unitario'];
            });
    }

    public function salvarCompra(): void
    {
        if ($this->form['tipo'] === 'despesa') {
            $this->salvarDespesa();
            return;
        }

        $this->validate([
            'form.data_compra' => ['required', 'date'],
            'form.fornecedor_id' => ['nullable', 'integer', 'exists:fornecedores,id'],
            'form.condicao_pagamento' => ['required', 'in:a_vista,parcelado'],
            'form.conta_paga_no_ato' => ['boolean'],
            'form.data_primeiro_vencimento' => ['nullable', 'date'],
            'form.quantidade_parcelas' => ['nullable', 'integer', 'min:2', 'max:60'],
            'form.intervalo_parcelas_dias' => ['nullable', 'integer', 'min:1', 'max:365'],
            'form.observacoes' => ['nullable', 'string', 'max:2000'],
            'itensCompra' => ['array', 'min:1'],
            'itensCompra.*.insumo_id' => ['nullable', 'integer', 'exists:insumos,id'],
            'itensCompra.*.quantidade' => ['nullable', 'numeric', 'gt:0'],
            'itensCompra.*.valor_unitario' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($this->form['condicao_pagamento'] === 'parcelado') {
            if ((int) ($this->form['quantidade_parcelas'] ?? 0) < 2) {
                $this->addError('form.quantidade_parcelas', 'Informe ao menos 2 parcelas para pagamento parcelado.');
                return;
            }

            if (($this->form['data_primeiro_vencimento'] ?? '') === '') {
                $this->addError('form.data_primeiro_vencimento', 'Informe a data do primeiro vencimento.');
                return;
            }
        }

        if ($this->form['condicao_pagamento'] === 'a_vista' && ! (bool) $this->form['conta_paga_no_ato'] && ($this->form['data_primeiro_vencimento'] ?? '') === '') {
            $this->addError('form.data_primeiro_vencimento', 'Informe a data de vencimento para conta a pagar em aberto.');
            return;
        }

        $itensValidos = collect($this->itensCompra)
            ->filter(function (array $item) {
                return ($item['insumo_id'] ?? '') !== ''
                    && ($item['quantidade'] ?? '') !== ''
                    && ($item['valor_unitario'] ?? '') !== ''
                    && (float) $item['quantidade'] > 0
                    && (float) $item['valor_unitario'] >= 0;
            })
            ->values();

        if ($itensValidos->isEmpty()) {
            $this->addError('itensCompra', 'Informe ao menos um item de insumo com quantidade e valor unitario.');
            return;
        }

        $itensConsolidados = $itensValidos
            ->groupBy(function (array $item) {
                return (int) $item['insumo_id'];
            })
            ->map(function ($grupo, $insumoId) {
                $quantidadeTotal = (float) $grupo->sum(function (array $item) {
                    return (float) $item['quantidade'];
                });

                $valorTotal = (float) $grupo->sum(function (array $item) {
                    return (float) $item['quantidade'] * (float) $item['valor_unitario'];
                });

                $valorUnitarioMedioEntrada = $quantidadeTotal > 0
                    ? $valorTotal / $quantidadeTotal
                    : 0;

                return [
                    'insumo_id' => (int) $insumoId,
                    'quantidade' => $quantidadeTotal,
                    'valor_unitario' => $valorUnitarioMedioEntrada,
                    'valor_total' => $valorTotal,
                ];
            })
            ->values();

        $valorTotalCompra = (float) $itensConsolidados->sum(function (array $item) {
            return (float) $item['valor_total'];
        });

        DB::transaction(function () use ($itensConsolidados, $valorTotalCompra) {
            $compra = Compra::query()->create([
                'data_compra' => $this->form['data_compra'],
                'fornecedor_id' => $this->form['fornecedor_id'] !== '' ? (int) $this->form['fornecedor_id'] : null,
                'valor_total' => $valorTotalCompra,
                'observacoes' => $this->form['observacoes'] !== '' ? $this->form['observacoes'] : null,
            ]);

            $fifoService = app(InsumoFifoService::class);

            foreach ($itensConsolidados as $item) {
                $insumo = Insumo::query()->findOrFail((int) $item['insumo_id']);
                $quantidade = (float) $item['quantidade'];
                $valorUnitarioEntrada = (float) $item['valor_unitario'];
                $valorTotalItem = (float) $item['valor_total'];

                $compraItem = $compra->itens()->create([
                    'insumo_id' => $insumo->id,
                    'quantidade' => $quantidade,
                    'unidade' => $insumo->unidade,
                    'valor_unitario' => $valorUnitarioEntrada,
                    'valor_total' => $valorTotalItem,
                ]);

                $fifoService->registrarEntrada(
                    $insumo,
                    $quantidade,
                    $valorUnitarioEntrada,
                    $compra->data_compra,
                    $compraItem->id,
                );
            }

            $contaPagaNoAto = (bool) $this->form['conta_paga_no_ato'];
            $condicaoPagamento = $this->form['condicao_pagamento'];

            if ($condicaoPagamento === 'a_vista') {
                $dataVencimento = $contaPagaNoAto
                    ? Carbon::parse($this->form['data_compra'])->toDateString()
                    : $this->form['data_primeiro_vencimento'];

                ContaPagar::query()->create([
                    'compra_id' => $compra->id,
                    'fornecedor_id' => $compra->fornecedor_id,
                    'descricao' => 'Compra #' . $compra->id . ' - Parcela unica',
                    'data_vencimento' => $dataVencimento,
                    'valor_original' => $valorTotalCompra,
                    'valor_aberto' => $contaPagaNoAto ? 0 : $valorTotalCompra,
                    'status' => $contaPagaNoAto ? 'pago' : 'pendente',
                    'data_pagamento' => $contaPagaNoAto ? Carbon::parse($this->form['data_compra']) : null,
                    'observacoes' => $compra->observacoes,
                ]);
            }

            if ($condicaoPagamento === 'parcelado') {
                $parcelas = (int) $this->form['quantidade_parcelas'];
                $intervaloDias = (int) $this->form['intervalo_parcelas_dias'];
                $primeiroVencimento = Carbon::parse($this->form['data_primeiro_vencimento']);

                $valorBaseParcela = round($valorTotalCompra / $parcelas, 4);
                $acumulado = 0.0;

                for ($i = 1; $i <= $parcelas; $i++) {
                    $valorParcela = $i < $parcelas
                        ? $valorBaseParcela
                        : round($valorTotalCompra - $acumulado, 4);

                    $acumulado += $valorParcela;

                    ContaPagar::query()->create([
                        'compra_id' => $compra->id,
                        'fornecedor_id' => $compra->fornecedor_id,
                        'descricao' => 'Compra #' . $compra->id . ' - Parcela ' . $i . '/' . $parcelas,
                        'data_vencimento' => $primeiroVencimento->copy()->addDays(($i - 1) * $intervaloDias)->toDateString(),
                        'valor_original' => $valorParcela,
                        'valor_aberto' => $valorParcela,
                        'status' => 'pendente',
                        'observacoes' => $compra->observacoes,
                    ]);
                }
            }
        });

        session()->flash('status', 'Compra registrada, contas a pagar geradas e custo medio do insumo atualizado.');
        $this->closeFormModal();
        $this->resetPage();
    }

    public function salvarDespesa(): void
    {
        $this->validate([
            'form.data_compra'               => ['required', 'date'],
            'form.fornecedor_id'             => ['nullable', 'integer', 'exists:fornecedores,id'],
            'form.categoria_despesa'         => ['required', 'string', 'max:100'],
            'form.descricao_despesa'         => ['required', 'string', 'max:500'],
            'form.valor_despesa'             => ['required', 'numeric', 'gt:0'],
            'form.condicao_pagamento'        => ['required', 'in:a_vista,parcelado'],
            'form.conta_paga_no_ato'         => ['boolean'],
            'form.data_primeiro_vencimento'  => ['nullable', 'date'],
            'form.quantidade_parcelas'       => ['nullable', 'integer', 'min:2', 'max:60'],
            'form.intervalo_parcelas_dias'   => ['nullable', 'integer', 'min:1', 'max:365'],
            'form.observacoes'               => ['nullable', 'string', 'max:2000'],
        ]);

        if ($this->form['condicao_pagamento'] === 'parcelado') {
            if ((int) ($this->form['quantidade_parcelas'] ?? 0) < 2) {
                $this->addError('form.quantidade_parcelas', 'Informe ao menos 2 parcelas.');
                return;
            }
            if (($this->form['data_primeiro_vencimento'] ?? '') === '') {
                $this->addError('form.data_primeiro_vencimento', 'Informe a data do primeiro vencimento.');
                return;
            }
        }

        if ($this->form['condicao_pagamento'] === 'a_vista' && !(bool) $this->form['conta_paga_no_ato'] && ($this->form['data_primeiro_vencimento'] ?? '') === '') {
            $this->addError('form.data_primeiro_vencimento', 'Informe a data de vencimento.');
            return;
        }

        $valorTotal = (float) $this->form['valor_despesa'];
        $categoriasMap = self::CATEGORIAS_DESPESA;
        $nomeCategoria = $categoriasMap[$this->form['categoria_despesa']] ?? $this->form['categoria_despesa'];

        DB::transaction(function () use ($valorTotal, $nomeCategoria) {
            $compra = Compra::query()->create([
                'tipo'               => 'despesa',
                'categoria_despesa'  => $this->form['categoria_despesa'],
                'data_compra'        => $this->form['data_compra'],
                'fornecedor_id'      => $this->form['fornecedor_id'] !== '' ? (int) $this->form['fornecedor_id'] : null,
                'valor_total'        => $valorTotal,
                'observacoes'        => $this->form['observacoes'] !== '' ? $this->form['observacoes'] : null,
            ]);

            $contaPagaNoAto     = (bool) $this->form['conta_paga_no_ato'];
            $condicaoPagamento  = $this->form['condicao_pagamento'];
            $descricaoBase      = $nomeCategoria . ': ' . $this->form['descricao_despesa'];

            if ($condicaoPagamento === 'a_vista') {
                $dataVencimento = $contaPagaNoAto
                    ? Carbon::parse($this->form['data_compra'])->toDateString()
                    : $this->form['data_primeiro_vencimento'];

                ContaPagar::query()->create([
                    'compra_id'      => $compra->id,
                    'fornecedor_id'  => $compra->fornecedor_id,
                    'descricao'      => $descricaoBase,
                    'data_vencimento'=> $dataVencimento,
                    'valor_original' => $valorTotal,
                    'valor_aberto'   => $contaPagaNoAto ? 0 : $valorTotal,
                    'status'         => $contaPagaNoAto ? 'pago' : 'pendente',
                    'data_pagamento' => $contaPagaNoAto ? Carbon::parse($this->form['data_compra']) : null,
                    'observacoes'    => $compra->observacoes,
                ]);
            }

            if ($condicaoPagamento === 'parcelado') {
                $parcelas       = (int) $this->form['quantidade_parcelas'];
                $intervaloDias  = (int) $this->form['intervalo_parcelas_dias'];
                $primeiroVenc   = Carbon::parse($this->form['data_primeiro_vencimento']);
                $valorBase      = round($valorTotal / $parcelas, 4);
                $acumulado      = 0.0;

                for ($i = 1; $i <= $parcelas; $i++) {
                    $valorParcela = $i < $parcelas
                        ? $valorBase
                        : round($valorTotal - $acumulado, 4);
                    $acumulado += $valorParcela;

                    ContaPagar::query()->create([
                        'compra_id'       => $compra->id,
                        'fornecedor_id'   => $compra->fornecedor_id,
                        'descricao'       => $descricaoBase . ' - Parcela ' . $i . '/' . $parcelas,
                        'data_vencimento' => $primeiroVenc->copy()->addDays(($i - 1) * $intervaloDias)->toDateString(),
                        'valor_original'  => $valorParcela,
                        'valor_aberto'    => $valorParcela,
                        'status'          => 'pendente',
                        'observacoes'     => $compra->observacoes,
                    ]);
                }
            }
        });

        session()->flash('status', 'Despesa registrada e conta a pagar gerada.');
        $this->closeFormModal();
        $this->resetPage();
    }

    protected function resetForm(): void
    {
        $this->form = [
            'tipo' => 'compra',
            'data_compra' => now()->format('Y-m-d\\TH:i'),
            'fornecedor_id' => '',
            'condicao_pagamento' => 'a_vista',
            'conta_paga_no_ato' => false,
            'data_primeiro_vencimento' => now()->addDays(30)->format('Y-m-d'),
            'quantidade_parcelas' => 2,
            'intervalo_parcelas_dias' => 30,
            'observacoes' => '',
            'categoria_despesa' => '',
            'descricao_despesa' => '',
            'valor_despesa' => '',
        ];

        $this->itensCompra = [
            ['insumo_id' => '', 'quantidade' => '', 'valor_unitario' => ''],
        ];

        $this->resetValidation();
    }

    public function render()
    {
        $compras = Compra::query()
            ->with(['fornecedor', 'contasPagar'])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('id', 'like', '%' . $this->search . '%')
                        ->orWhereHas('fornecedor', function ($fornecedorQuery) {
                            $fornecedorQuery->where('nome', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->latest('data_compra')
            ->paginate(10);

        $fornecedoresAtivos = Fornecedor::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->get(['id', 'nome']);

        $insumosAtivos = Insumo::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->get(['id', 'nome', 'codigo', 'unidade', 'custo_unitario']);

        $compraDetalhes = null;

        if ($this->showDetailsModal && $this->detailsCompraId !== null) {
            $compraDetalhes = Compra::query()
                ->with(['fornecedor', 'itens.insumo', 'contasPagar'])
                ->find($this->detailsCompraId);

            if ($compraDetalhes === null) {
                $this->closeDetailsModal();
            }
        }

        return view('livewire.compras.index', [
            'compras' => $compras,
            'compraDetalhes' => $compraDetalhes,
            'fornecedoresAtivos' => $fornecedoresAtivos,
            'insumosAtivos' => $insumosAtivos,
            'categoriasDespesa' => self::CATEGORIAS_DESPESA,
        ]);
    }
}
