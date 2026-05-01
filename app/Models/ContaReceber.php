<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'venda_id',
    'cliente_id',
    'descricao',
    'data_vencimento',
    'valor_original',
    'valor_aberto',
    'status',
    'data_recebimento',
    'observacoes',
])]
class ContaReceber extends Model
{
    protected $table = 'contas_receber';

    protected function casts(): array
    {
        return [
            'data_vencimento' => 'date',
            'data_recebimento' => 'datetime',
            'valor_original' => 'decimal:4',
            'valor_aberto' => 'decimal:4',
        ];
    }

    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
