<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'tipo',
    'nome',
    'documento',
    'email',
    'telefone',
    'cidade',
    'uf',
    'endereco',
    'ativo',
    'observacoes',
])]
class Cliente extends Model
{
    use SoftDeletes;

    public function vendas(): HasMany
    {
        return $this->hasMany(Venda::class);
    }

    public function contasReceber(): HasMany
    {
        return $this->hasMany(ContaReceber::class);
    }
}
