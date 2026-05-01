<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'producao_id',
    'produto_id',
    'quantidade_produzida',
    'unidade',
])]
class ProducaoItem extends Model
{
    protected function casts(): array
    {
        return [
            'quantidade_produzida' => 'decimal:4',
        ];
    }

    public function producao(): BelongsTo
    {
        return $this->belongsTo(Producao::class);
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }
}
