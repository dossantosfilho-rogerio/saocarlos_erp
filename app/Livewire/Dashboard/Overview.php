<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Overview extends Component
{
    public function getKpis(): array
    {
        $now = now();
        $year = $now->year;
        $month = $now->month;
        $weekStart = $now->copy()->startOfWeek()->toDateString();
        $weekEnd   = $now->copy()->endOfWeek()->toDateString();

        $vendas_semana = (float) DB::table('vendas')
            ->whereBetween('data_venda', [$weekStart, $weekEnd])
            ->sum('valor_total');

        $vendas_mes = (float) DB::table('vendas')
            ->whereYear('data_venda', $year)
            ->whereMonth('data_venda', $month)
            ->sum('valor_total');

        $volume_kg = (float) DB::table('producao_items')
            ->join('producoes', 'producao_items.producao_id', '=', 'producoes.id')
            ->whereYear('producoes.data_producao', $year)
            ->whereMonth('producoes.data_producao', $month)
            ->sum('producao_items.quantidade_produzida');

        $custo_mes = (float) DB::table('venda_items')
            ->join('vendas', 'venda_items.venda_id', '=', 'vendas.id')
            ->join('produtos', 'venda_items.produto_id', '=', 'produtos.id')
            ->whereYear('vendas.data_venda', $year)
            ->whereMonth('vendas.data_venda', $month)
            ->sum(DB::raw('venda_items.quantidade * produtos.custo_unitario'));

        $despesas_mes = (float) DB::table('compras')
            ->where('tipo', 'despesa')
            ->whereYear('data_compra', $year)
            ->whereMonth('data_compra', $month)
            ->sum('valor_total');

        $lucro_estimado = $vendas_mes - $custo_mes - $despesas_mes;

        $a_receber = (float) DB::table('contas_receber')
            ->whereIn('status', ['pendente', 'parcial'])
            ->sum('valor_aberto');

        $a_pagar = (float) DB::table('contas_pagar')
            ->whereIn('status', ['pendente', 'parcial'])
            ->sum('valor_aberto');

        $produtos_em_estoque = (int) DB::table('produtos')
            ->where('estoque_atual', '>', 0)
            ->count();

        $insumos_ativos = (int) DB::table('insumos')
            ->where('ativo', 1)
            ->count();

        return [
            'vendas_semana'       => $vendas_semana,
            'vendas_mes'          => $vendas_mes,
            'volume_kg'           => $volume_kg,
            'lucro_estimado'      => $lucro_estimado,
            'a_receber'           => $a_receber,
            'a_pagar'             => $a_pagar,
            'produtos_em_estoque' => $produtos_em_estoque,
            'insumos_ativos'      => $insumos_ativos,
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.overview', [
            'kpis' => $this->getKpis(),
        ]);
    }
}
