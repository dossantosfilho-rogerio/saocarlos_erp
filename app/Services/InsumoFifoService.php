<?php

namespace App\Services;

use App\Models\Insumo;
use App\Models\InsumoLote;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use RuntimeException;

class InsumoFifoService
{
    public function registrarEntrada(Insumo $insumo, float $quantidade, float $custoUnitario, ?CarbonInterface $dataEntrada = null, ?int $compraItemId = null): void
    {
        if ($quantidade <= 0) {
            throw new RuntimeException('Quantidade de entrada deve ser maior que zero.');
        }

        InsumoLote::query()->create([
            'insumo_id' => $insumo->id,
            'compra_item_id' => $compraItemId,
            'data_entrada' => ($dataEntrada ?? now())->format('Y-m-d H:i:s'),
            'quantidade_entrada' => $quantidade,
            'quantidade_saldo' => $quantidade,
            'custo_unitario' => $custoUnitario,
        ]);

        $this->recalcularEstoqueECusto($insumo);
    }

    public function estimarSaida(Insumo $insumo, float $quantidade): array
    {
        if ($quantidade <= 0) {
            throw new RuntimeException('Quantidade de saida deve ser maior que zero.');
        }

        $lotes = $this->consultarLotesDisponiveis($insumo, false);
        return $this->calcularConsumoFifo($lotes, $quantidade, false);
    }

    public function consumir(Insumo $insumo, float $quantidade): array
    {
        if ($quantidade <= 0) {
            throw new RuntimeException('Quantidade de saida deve ser maior que zero.');
        }

        $lotes = $this->consultarLotesDisponiveis($insumo, true);
        $consumo = $this->calcularConsumoFifo($lotes, $quantidade, true);
        $this->recalcularEstoqueECusto($insumo);

        return $consumo;
    }

    protected function consultarLotesDisponiveis(Insumo $insumo, bool $lock): Collection
    {
        $query = InsumoLote::query()
            ->where('insumo_id', $insumo->id)
            ->where('quantidade_saldo', '>', 0)
            ->orderBy('data_entrada')
            ->orderBy('id');

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->get();
    }

    protected function calcularConsumoFifo(Collection $lotes, float $quantidade, bool $persist): array
    {
        $disponivel = (float) $lotes->sum(function (InsumoLote $lote) {
            return (float) $lote->quantidade_saldo;
        });

        if ($disponivel < $quantidade) {
            throw new RuntimeException('Estoque de insumo insuficiente para esta producao.');
        }

        $restante = $quantidade;
        $custoTotal = 0.0;

        foreach ($lotes as $lote) {
            if ($restante <= 0) {
                break;
            }

            $saldoAtual = (float) $lote->quantidade_saldo;
            if ($saldoAtual <= 0) {
                continue;
            }

            $consumido = min($saldoAtual, $restante);
            $restante -= $consumido;
            $custoTotal += $consumido * (float) $lote->custo_unitario;

            if ($persist) {
                $lote->update([
                    'quantidade_saldo' => $saldoAtual - $consumido,
                ]);
            }
        }

        return [
            'quantidade' => $quantidade,
            'custo_total' => $custoTotal,
            'custo_unitario_movimentacao' => $quantidade > 0 ? ($custoTotal / $quantidade) : 0,
        ];
    }

    public function recalcularInsumo(Insumo $insumo): void
    {
        $this->recalcularEstoqueECusto($insumo);
    }

    protected function recalcularEstoqueECusto(Insumo $insumo): void
    {
        $lotes = InsumoLote::query()
            ->where('insumo_id', $insumo->id)
            ->where('quantidade_saldo', '>', 0)
            ->get();

        $estoqueAtual = (float) $lotes->sum(function (InsumoLote $lote) {
            return (float) $lote->quantidade_saldo;
        });

        $valorTotalEstoque = (float) $lotes->sum(function (InsumoLote $lote) {
            return (float) $lote->quantidade_saldo * (float) $lote->custo_unitario;
        });

        $custoUnitarioMedio = $estoqueAtual > 0 ? ($valorTotalEstoque / $estoqueAtual) : 0;

        $insumo->update([
            'estoque_atual' => $estoqueAtual,
            'custo_unitario' => $custoUnitarioMedio,
        ]);
    }
}
