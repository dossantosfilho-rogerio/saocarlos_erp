<?php

namespace App\Livewire\Contas;

use App\Models\ContaPagar;
use App\Models\ContaReceber;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $aba = 'a_receber';
    public string $search = '';
    public string $filtroStatus = '';

    // Baixa modal
    public bool $showBaixaModal = false;
    public int $baixaId = 0;
    public string $baixaTipo = '';
    public string $baixaValorPago = '';
    public array $baixaInfo = [];

    public function updatedAba(): void
    {
        $this->resetPage();
        $this->search = '';
        $this->filtroStatus = '';
        $this->resetErrorBag();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroStatus(): void
    {
        $this->resetPage();
    }

    public function abrirBaixa(int $id, string $tipo): void
    {
        $this->baixaId = $id;
        $this->baixaTipo = $tipo;
        $this->baixaValorPago = '';
        $this->resetErrorBag();

        if ($tipo === 'receber') {
            $conta = ContaReceber::with('cliente')->findOrFail($id);
            $this->baixaInfo = [
                'descricao'    => $conta->descricao,
                'entidade'     => $conta->cliente->nome ?? '—',
                'valor_aberto' => (float) $conta->valor_aberto,
                'vencimento'   => $conta->data_vencimento->format('d/m/Y'),
            ];
        } else {
            $conta = ContaPagar::with('fornecedor')->findOrFail($id);
            $this->baixaInfo = [
                'descricao'    => $conta->descricao,
                'entidade'     => $conta->fornecedor->nome ?? '—',
                'valor_aberto' => (float) $conta->valor_aberto,
                'vencimento'   => $conta->data_vencimento->format('d/m/Y'),
            ];
        }

        $this->showBaixaModal = true;
    }

    public function fecharBaixaModal(): void
    {
        $this->showBaixaModal = false;
        $this->baixaId = 0;
        $this->baixaInfo = [];
        $this->baixaValorPago = '';
        $this->resetErrorBag();
    }

    public function registrarBaixa(): void
    {
        $valorPago = (float) str_replace(',', '.', $this->baixaValorPago);

        if ($valorPago <= 0) {
            $this->addError('baixaValorPago', 'O valor deve ser maior que zero.');
            return;
        }

        $aberto = (float) ($this->baixaInfo['valor_aberto'] ?? 0);

        if ($valorPago > $aberto + 0.0001) {
            $this->addError('baixaValorPago', 'Valor não pode exceder o saldo em aberto (R$ ' . number_format($aberto, 2, ',', '.') . ').');
            return;
        }

        DB::transaction(function () use ($valorPago) {
            if ($this->baixaTipo === 'receber') {
                $conta = ContaReceber::findOrFail($this->baixaId);
                $novoAberto = (float) $conta->valor_aberto - $valorPago;
                $novoAberto = max(0, round($novoAberto, 4));
                $conta->update([
                    'valor_aberto'     => $novoAberto,
                    'status'           => $novoAberto <= 0.001 ? 'pago' : 'parcial',
                    'data_recebimento' => $novoAberto <= 0.001 ? now() : $conta->data_recebimento,
                ]);
            } else {
                $conta = ContaPagar::findOrFail($this->baixaId);
                $novoAberto = (float) $conta->valor_aberto - $valorPago;
                $novoAberto = max(0, round($novoAberto, 4));
                $conta->update([
                    'valor_aberto'   => $novoAberto,
                    'status'         => $novoAberto <= 0.001 ? 'pago' : 'parcial',
                    'data_pagamento' => $novoAberto <= 0.001 ? now() : $conta->data_pagamento,
                ]);
            }
        });

        $this->fecharBaixaModal();
    }

    public function render()
    {
        $search      = $this->search;
        $filtroStatus = $this->filtroStatus;

        if ($this->aba === 'a_receber') {
            $base = ContaReceber::with('cliente')
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($inner) use ($search) {
                        $inner->where('descricao', 'like', "%{$search}%")
                              ->orWhereHas('cliente', fn($c) => $c->where('nome', 'like', "%{$search}%"));
                    });
                })
                ->when($filtroStatus, fn($q) => $q->where('status', $filtroStatus));

            $totalAberto   = (float) (clone $base)->sum('valor_aberto');
            $totalOriginal = (float) (clone $base)->sum('valor_original');

            $contas = $base->orderBy('data_vencimento')->paginate(15);
        } else {
            $base = ContaPagar::with('fornecedor')
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($inner) use ($search) {
                        $inner->where('descricao', 'like', "%{$search}%")
                              ->orWhereHas('fornecedor', fn($c) => $c->where('nome', 'like', "%{$search}%"));
                    });
                })
                ->when($filtroStatus, fn($q) => $q->where('status', $filtroStatus));

            $totalAberto   = (float) (clone $base)->sum('valor_aberto');
            $totalOriginal = (float) (clone $base)->sum('valor_original');

            $contas = $base->orderBy('data_vencimento')->paginate(15);
        }

        return view('livewire.contas.index', compact('contas', 'totalAberto', 'totalOriginal'));
    }
}
