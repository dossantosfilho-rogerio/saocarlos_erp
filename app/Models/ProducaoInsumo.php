<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'producao_id',
    'insumo_id',
    'quantidade_consumida',
    'unidade',
    'custo_unitario',
    'custo_total',
])]
class ProducaoInsumo extends Model
{
    protected function casts(): array
    {
        return [
            'quantidade_consumida' => 'decimal:4',
            'custo_unitario' => 'decimal:4',
            'custo_total' => 'decimal:4',
        ];
    }

    public function producao(): BelongsTo
    {
        return $this->belongsTo(Producao::class);
    }

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class);
    }
}
