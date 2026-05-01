<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'insumo_id',
    'compra_item_id',
    'data_entrada',
    'quantidade_entrada',
    'quantidade_saldo',
    'custo_unitario',
])]
class InsumoLote extends Model
{
    protected $table = 'insumo_lotes';

    protected function casts(): array
    {
        return [
            'data_entrada' => 'datetime',
            'quantidade_entrada' => 'decimal:4',
            'quantidade_saldo' => 'decimal:4',
            'custo_unitario' => 'decimal:4',
        ];
    }

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class);
    }

    public function compraItem(): BelongsTo
    {
        return $this->belongsTo(CompraItem::class);
    }
}
