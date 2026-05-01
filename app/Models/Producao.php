<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'data_producao',
    'total_insumos_consumidos',
    'unidade_total_insumos',
    'observacoes',
])]
class Producao extends Model
{
    protected $table = 'producoes';

    protected function casts(): array
    {
        return [
            'data_producao' => 'datetime',
            'total_insumos_consumidos' => 'decimal:4',
        ];
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ProducaoItem::class);
    }

    public function insumos(): HasMany
    {
        return $this->hasMany(ProducaoInsumo::class);
    }
}
