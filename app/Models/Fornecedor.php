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
class Fornecedor extends Model
{
    use SoftDeletes;

    protected $table = 'fornecedores';

    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }

    public function contasPagar(): HasMany
    {
        return $this->hasMany(ContaPagar::class);
    }
}
