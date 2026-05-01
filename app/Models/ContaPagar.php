<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'compra_id',
    'fornecedor_id',
    'descricao',
    'data_vencimento',
    'valor_original',
    'valor_aberto',
    'status',
    'data_pagamento',
    'observacoes',
])]
class ContaPagar extends Model
{
    protected $table = 'contas_pagar';

    protected function casts(): array
    {
        return [
            'data_vencimento' => 'date',
            'data_pagamento' => 'datetime',
            'valor_original' => 'decimal:4',
            'valor_aberto' => 'decimal:4',
        ];
    }

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class);
    }
}
