<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'nome',
    'sku',
    'unidade_padrao',
    'custo_unitario',
    'estoque_atual',
    'ativo',
    'observacoes',
])]
class Produto extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'custo_unitario' => 'decimal:4',
            'estoque_atual' => 'decimal:4',
        ];
    }

    public function producaoItens(): HasMany
    {
        return $this->hasMany(ProducaoItem::class);
    }

    public function vendaItens(): HasMany
    {
        return $this->hasMany(VendaItem::class);
    }
}
