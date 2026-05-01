<?php

namespace App\Livewire\Estoque;

use App\Models\Insumo;
use App\Models\Producao;
use App\Models\Produto;
use App\Services\InsumoFifoService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public bool $showProducaoModal = false;

    public string $search = '';

    public array $producaoForm = [
        'data_producao' => '',
        'observacoes' => '',
    ];

    public array $itens = [
        ['produto_id' => '', 'quantidade_produzida' => ''],
    ];

    public array $insumosConsumidos = [
        ['insumo_id' => '', 'quantidade_consumida' => ''],
    ];

    public function mount(): void
    {
        $this->resetProducaoForm();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openProducaoModal(): void
    {
        $this->resetProducaoForm();
        $this->showProducaoModal = true;
    }

    public function closeProducaoModal(): void
    {
        $this->showProducaoModal = false;
        $this->resetProducaoForm();
    }

    public function addItemLinha(): void
    {
        $this->itens[] = ['produto_id' => '', 'quantidade_produzida' => ''];
    }

    public function removeItemLinha(int $index): void
    {
        if (count($this->itens) <= 1) {
            return;
        }

        unset($this->itens[$index]);
        $this->itens = array_values($this->itens);
    }

    public function addInsumoLinha(): void
    {
        $this->insumosConsumidos[] = ['insumo_id' => '', 'quantidade_consumida' => ''];
    }

    public function removeInsumoLinha(int $index): void
    {
        if (count($this->insumosConsumidos) <= 1) {
            return;
        }

        unset($this->insumosConsumidos[$index]);
        $this->insumosConsumidos = array_values($this->insumosConsumidos);
    }

    public function salvarProducao(): void
    {
        $this->validate([
            'producaoForm.data_producao' => ['required', 'date'],
            'producaoForm.observacoes' => ['nullable', 'string', 'max:2000'],
            'itens' => ['array', 'min:1'],
            'itens.*.produto_id' => ['nullable', 'integer', 'exists:produtos,id'],
            'itens.*.quantidade_produzida' => ['nullable', 'numeric', 'gt:0'],
            'insumosConsumidos' => ['array', 'min:1'],
            'insumosConsumidos.*.insumo_id' => ['nullable', 'integer', 'exists:insumos,id'],
            'insumosConsumidos.*.quantidade_consumida' => ['nullable', 'numeric', 'gt:0'],
        ]);

        $itensValidos = collect($this->itens)
            ->filter(function (array $item) {
                return ($item['produto_id'] ?? '') !== ''
                    && ($item['quantidade_produzida'] ?? '') !== ''
                    && (float) $item['quantidade_produzida'] > 0;
            })
            ->values();

        if ($itensValidos->isEmpty()) {
            $this->addError('itens', 'Informe ao menos um produto com quantidade produzida.');
            return;
        }

        $insumosValidos = collect($this->insumosConsumidos)
            ->filter(function (array $item) {
                return ($item['insumo_id'] ?? '') !== ''
                    && ($item['quantidade_consumida'] ?? '') !== ''
                    && (float) $item['quantidade_consumida'] > 0;
            })
            ->values();

        if ($insumosValidos->isEmpty()) {
            $this->addError('insumosConsumidos', 'Informe ao menos um insumo consumido.');
            return;
        }

        $quantidadeTotalProduzida = (float) $itensValidos->sum(function (array $item) {
            return (float) $item['quantidade_produzida'];
        });

        if ($quantidadeTotalProduzida <= 0) {
            $this->addError('itens', 'A quantidade total produzida deve ser maior que zero.');
            return;
        }

        $totalInsumosConsumidos = (float) $insumosValidos->sum(function (array $item) {
            return (float) $item['quantidade_consumida'];
        });

        DB::transaction(function () use ($itensValidos, $insumosValidos, $totalInsumosConsumidos, $quantidadeTotalProduzida) {
            $producao = Producao::query()->create([
                'data_producao' => $this->producaoForm['data_producao'],
                'total_insumos_consumidos' => $totalInsumosConsumidos,
                'observacoes' => $this->producaoForm['observacoes'] !== '' ? $this->producaoForm['observacoes'] : null,
            ]);

            $fifoService = app(InsumoFifoService::class);
            $custoTotalInsumos = 0.0;

            foreach ($insumosValidos as $insumoItem) {
                $insumo = Insumo::query()->findOrFail((int) $insumoItem['insumo_id']);
                $quantidadeConsumida = (float) $insumoItem['quantidade_consumida'];
                $consumo = $fifoService->consumir($insumo, $quantidadeConsumida);

                $custoTotalInsumos += (float) $consumo['custo_total'];

                $producao->insumos()->create([
                    'insumo_id' => (int) $insumoItem['insumo_id'],
                    'quantidade_consumida' => $quantidadeConsumida,
                    'unidade' => $insumo?->unidade,
                    'custo_unitario' => (float) $consumo['custo_unitario_movimentacao'],
                    'custo_total' => (float) $consumo['custo_total'],
                ]);
            }

            $custoUnitarioProduzido = $custoTotalInsumos / $quantidadeTotalProduzida;

            foreach ($itensValidos as $item) {
                $produto = Produto::query()->findOrFail((int) $item['produto_id']);
                $quantidade = (float) $item['quantidade_produzida'];

                $producao->itens()->create([
                    'produto_id' => $produto->id,
                    'quantidade_produzida' => $quantidade,
                    'unidade' => $produto->unidade_padrao,
                ]);

                $produto->update([
                    'estoque_atual' => (float) $produto->estoque_atual + $quantidade,
                    'custo_unitario' => $custoUnitarioProduzido,
                ]);
            }
        });

        session()->flash('status', 'Producao registrada, estoque atualizado e custo unitario recalculado.');
        $this->closeProducaoModal();
        $this->resetPage();
    }

    protected function resetProducaoForm(): void
    {
        $this->producaoForm = [
            'data_producao' => now()->format('Y-m-d\\TH:i'),
            'observacoes' => '',
        ];

        $this->itens = [
            ['produto_id' => '', 'quantidade_produzida' => ''],
        ];

        $this->insumosConsumidos = [
            ['insumo_id' => '', 'quantidade_consumida' => ''],
        ];

        $this->resetValidation();
    }

    public function custoTotalInsumosCalculado(): float
    {
        $insumosValidos = collect($this->insumosConsumidos)
            ->filter(function (array $item) {
                return ($item['insumo_id'] ?? '') !== ''
                    && ($item['quantidade_consumida'] ?? '') !== ''
                    && (float) $item['quantidade_consumida'] > 0;
            });

        if ($insumosValidos->isEmpty()) {
            return 0;
        }

        $fifoService = app(InsumoFifoService::class);

        return (float) $insumosValidos->sum(function (array $item) use ($fifoService) {
            $insumo = Insumo::query()->find((int) $item['insumo_id']);

            if (! $insumo) {
                return 0;
            }

            try {
                $estimativa = $fifoService->estimarSaida($insumo, (float) $item['quantidade_consumida']);
                return (float) $estimativa['custo_total'];
            } catch (\RuntimeException) {
                return 0;
            }
        });
    }

    public function quantidadeTotalProduzidaCalculada(): float
    {
        $itensValidos = collect($this->itens)
            ->filter(function (array $item) {
                return ($item['produto_id'] ?? '') !== ''
                    && ($item['quantidade_produzida'] ?? '') !== ''
                    && (float) $item['quantidade_produzida'] > 0;
            });

        return (float) $itensValidos->sum(function (array $item) {
            return (float) $item['quantidade_produzida'];
        });
    }

    public function custoUnitarioProduzidoCalculado(): float
    {
        $quantidadeTotalProduzida = $this->quantidadeTotalProduzidaCalculada();

        if ($quantidadeTotalProduzida <= 0) {
            return 0;
        }

        return $this->custoTotalInsumosCalculado() / $quantidadeTotalProduzida;
    }

    public function render()
    {
        $estoques = Produto::query()
            ->where('estoque_atual', '>', 0)
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('nome', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('estoque_atual')
            ->paginate(12);

        $produtosAtivos = Produto::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->get(['id', 'nome', 'sku', 'unidade_padrao']);

        $insumosAtivos = Insumo::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->get(['id', 'nome', 'codigo', 'unidade', 'custo_unitario']);

        $ultimasProducoes = Producao::query()
            ->latest('data_producao')
            ->limit(8)
            ->get();

        return view('livewire.estoque.index', [
            'estoques' => $estoques,
            'produtosAtivos' => $produtosAtivos,
            'insumosAtivos' => $insumosAtivos,
            'ultimasProducoes' => $ultimasProducoes,
        ]);
    }
}
