<?php

namespace App\Livewire\Vendas;

use App\Models\Cliente;
use App\Models\ContaReceber;
use App\Models\Produto;
use App\Models\Venda;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public bool $showFormModal = false;
    public string $search = '';

    public array $form = [
        'data_venda' => '',
        'cliente_id' => '',
        'condicao_recebimento' => 'a_vista',
        'conta_recebida_no_ato' => false,
        'data_primeiro_vencimento' => '',
        'quantidade_parcelas' => 2,
        'intervalo_parcelas_dias' => 30,
        'observacoes' => '',
    ];

    public array $itensVenda = [
        ['produto_id' => '', 'quantidade' => '', 'valor_unitario' => ''],
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

    public function addItemLinha(): void
    {
        $this->itensVenda[] = ['produto_id' => '', 'quantidade' => '', 'valor_unitario' => ''];
    }

    public function removeItemLinha(int $index): void
    {
        if (count($this->itensVenda) <= 1) {
            return;
        }

        unset($this->itensVenda[$index]);
        $this->itensVenda = array_values($this->itensVenda);
    }

    public function valorTotalVendaCalculado(): float
    {
        return (float) collect($this->itensVenda)
            ->filter(function (array $item) {
                return ($item['produto_id'] ?? '') !== ''
                    && ($item['quantidade'] ?? '') !== ''
                    && ($item['valor_unitario'] ?? '') !== ''
                    && (float) $item['quantidade'] > 0
                    && (float) $item['valor_unitario'] >= 0;
            })
            ->sum(function (array $item) {
                return (float) $item['quantidade'] * (float) $item['valor_unitario'];
            });
    }

    public function salvarVenda(): void
    {
        $this->validate([
            'form.data_venda' => ['required', 'date'],
            'form.cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'form.condicao_recebimento' => ['required', 'in:a_vista,parcelado'],
            'form.conta_recebida_no_ato' => ['boolean'],
            'form.data_primeiro_vencimento' => ['nullable', 'date'],
            'form.quantidade_parcelas' => ['nullable', 'integer', 'min:2', 'max:60'],
            'form.intervalo_parcelas_dias' => ['nullable', 'integer', 'min:1', 'max:365'],
            'form.observacoes' => ['nullable', 'string', 'max:2000'],
            'itensVenda' => ['array', 'min:1'],
            'itensVenda.*.produto_id' => ['nullable', 'integer', 'exists:produtos,id'],
            'itensVenda.*.quantidade' => ['nullable', 'numeric', 'gt:0'],
            'itensVenda.*.valor_unitario' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($this->form['condicao_recebimento'] === 'parcelado') {
            if ((int) ($this->form['quantidade_parcelas'] ?? 0) < 2) {
                $this->addError('form.quantidade_parcelas', 'Informe ao menos 2 parcelas para recebimento parcelado.');
                return;
            }

            if (($this->form['data_primeiro_vencimento'] ?? '') === '') {
                $this->addError('form.data_primeiro_vencimento', 'Informe a data do primeiro vencimento.');
                return;
            }
        }

        if ($this->form['condicao_recebimento'] === 'a_vista' && ! (bool) $this->form['conta_recebida_no_ato'] && ($this->form['data_primeiro_vencimento'] ?? '') === '') {
            $this->addError('form.data_primeiro_vencimento', 'Informe a data de vencimento para conta a receber em aberto.');
            return;
        }

        $itensValidos = collect($this->itensVenda)
            ->filter(function (array $item) {
                return ($item['produto_id'] ?? '') !== ''
                    && ($item['quantidade'] ?? '') !== ''
                    && ($item['valor_unitario'] ?? '') !== ''
                    && (float) $item['quantidade'] > 0
                    && (float) $item['valor_unitario'] >= 0;
            })
            ->values();

        if ($itensValidos->isEmpty()) {
            $this->addError('itensVenda', 'Informe ao menos um item de produto com quantidade e valor unitario.');
            return;
        }

        $itensConsolidados = $itensValidos
            ->groupBy(function (array $item) {
                return (int) $item['produto_id'];
            })
            ->map(function ($grupo, $produtoId) {
                $quantidadeTotal = (float) $grupo->sum(function (array $item) {
                    return (float) $item['quantidade'];
                });

                $valorTotal = (float) $grupo->sum(function (array $item) {
                    return (float) $item['quantidade'] * (float) $item['valor_unitario'];
                });

                $valorUnitarioMedioSaida = $quantidadeTotal > 0
                    ? $valorTotal / $quantidadeTotal
                    : 0;

                return [
                    'produto_id' => (int) $produtoId,
                    'quantidade' => $quantidadeTotal,
                    'valor_unitario' => $valorUnitarioMedioSaida,
                    'valor_total' => $valorTotal,
                ];
            })
            ->values();

        $valorTotalVenda = (float) $itensConsolidados->sum(function (array $item) {
            return (float) $item['valor_total'];
        });

        try {
            DB::transaction(function () use ($itensConsolidados, $valorTotalVenda) {
            $venda = Venda::query()->create([
                'data_venda' => $this->form['data_venda'],
                'cliente_id' => (int) $this->form['cliente_id'],
                'valor_total' => $valorTotalVenda,
                'observacoes' => $this->form['observacoes'] !== '' ? $this->form['observacoes'] : null,
            ]);

            foreach ($itensConsolidados as $item) {
                $produto = Produto::query()->lockForUpdate()->findOrFail((int) $item['produto_id']);
                $quantidade = (float) $item['quantidade'];

                if ((float) $produto->estoque_atual < $quantidade) {
                    throw new \RuntimeException('Estoque insuficiente para o produto ' . $produto->nome . '.');
                }

                $venda->itens()->create([
                    'produto_id' => $produto->id,
                    'quantidade' => $quantidade,
                    'unidade' => $produto->unidade_padrao,
                    'valor_unitario' => (float) $item['valor_unitario'],
                    'valor_total' => (float) $item['valor_total'],
                ]);

                $produto->update([
                    'estoque_atual' => (float) $produto->estoque_atual - $quantidade,
                ]);
            }

            $recebidaNoAto = (bool) $this->form['conta_recebida_no_ato'];
            $condicao = $this->form['condicao_recebimento'];

            if ($condicao === 'a_vista') {
                $dataVencimento = $recebidaNoAto
                    ? Carbon::parse($this->form['data_venda'])->toDateString()
                    : $this->form['data_primeiro_vencimento'];

                ContaReceber::query()->create([
                    'venda_id' => $venda->id,
                    'cliente_id' => $venda->cliente_id,
                    'descricao' => 'Venda #' . $venda->id . ' - Parcela unica',
                    'data_vencimento' => $dataVencimento,
                    'valor_original' => $valorTotalVenda,
                    'valor_aberto' => $recebidaNoAto ? 0 : $valorTotalVenda,
                    'status' => $recebidaNoAto ? 'recebido' : 'pendente',
                    'data_recebimento' => $recebidaNoAto ? Carbon::parse($this->form['data_venda']) : null,
                    'observacoes' => $venda->observacoes,
                ]);
            }

            if ($condicao === 'parcelado') {
                $parcelas = (int) $this->form['quantidade_parcelas'];
                $intervaloDias = (int) $this->form['intervalo_parcelas_dias'];
                $primeiroVencimento = Carbon::parse($this->form['data_primeiro_vencimento']);

                $valorBaseParcela = round($valorTotalVenda / $parcelas, 4);
                $acumulado = 0.0;

                for ($i = 1; $i <= $parcelas; $i++) {
                    $valorParcela = $i < $parcelas
                        ? $valorBaseParcela
                        : round($valorTotalVenda - $acumulado, 4);

                    $acumulado += $valorParcela;

                    ContaReceber::query()->create([
                        'venda_id' => $venda->id,
                        'cliente_id' => $venda->cliente_id,
                        'descricao' => 'Venda #' . $venda->id . ' - Parcela ' . $i . '/' . $parcelas,
                        'data_vencimento' => $primeiroVencimento->copy()->addDays(($i - 1) * $intervaloDias)->toDateString(),
                        'valor_original' => $valorParcela,
                        'valor_aberto' => $valorParcela,
                        'status' => 'pendente',
                        'observacoes' => $venda->observacoes,
                    ]);
                }
            }
            });
        } catch (\RuntimeException $e) {
            $this->addError('itensVenda', $e->getMessage());
            return;
        }

        session()->flash('status', 'Venda registrada, contas a receber geradas e estoque atualizado.');
        $this->closeFormModal();
        $this->resetPage();
    }

    protected function resetForm(): void
    {
        $this->form = [
            'data_venda' => now()->format('Y-m-d\\TH:i'),
            'cliente_id' => '',
            'condicao_recebimento' => 'a_vista',
            'conta_recebida_no_ato' => false,
            'data_primeiro_vencimento' => now()->addDays(30)->format('Y-m-d'),
            'quantidade_parcelas' => 2,
            'intervalo_parcelas_dias' => 30,
            'observacoes' => '',
        ];

        $this->itensVenda = [
            ['produto_id' => '', 'quantidade' => '', 'valor_unitario' => ''],
        ];

        $this->resetValidation();
    }

    public function render()
    {
        $vendas = Venda::query()
            ->with(['cliente', 'contasReceber'])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('id', 'like', '%' . $this->search . '%')
                        ->orWhereHas('cliente', function ($clienteQuery) {
                            $clienteQuery->where('nome', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->latest('data_venda')
            ->paginate(10);

        $clientesAtivos = Cliente::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->get(['id', 'nome']);

        $produtosAtivos = Produto::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->get(['id', 'nome', 'sku', 'unidade_padrao', 'estoque_atual']);

        return view('livewire.vendas.index', [
            'vendas' => $vendas,
            'clientesAtivos' => $clientesAtivos,
            'produtosAtivos' => $produtosAtivos,
        ]);
    }
}
