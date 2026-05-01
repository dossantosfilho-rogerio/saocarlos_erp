<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'tipo',
    'categoria_despesa',
    'data_compra',
    'fornecedor_id',
    'valor_total',
    'observacoes',
])]
class Compra extends Model
{
    protected function casts(): array
    {
        return [
            'data_compra' => 'datetime',
            'valor_total' => 'decimal:4',
        ];
    }

    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(CompraItem::class);
    }

    public function contasPagar(): HasMany
    {
        return $this->hasMany(ContaPagar::class);
    }
}
