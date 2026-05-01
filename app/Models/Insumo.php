<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'nome',
    'codigo',
    'unidade',
    'estoque_atual',
    'custo_unitario',
    'ativo',
    'observacoes',
])]
class Insumo extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'estoque_atual' => 'decimal:4',
            'custo_unitario' => 'decimal:4',
        ];
    }

    public function producaoInsumos(): HasMany
    {
        return $this->hasMany(ProducaoInsumo::class);
    }

    public function compraItens(): HasMany
    {
        return $this->hasMany(CompraItem::class);
    }

    public function lotes(): HasMany
    {
        return $this->hasMany(InsumoLote::class);
    }
}
