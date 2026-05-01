<?php

use App\Livewire\Auth\Login;
use App\Livewire\Clientes\Index as ClientesIndex;
use App\Livewire\Compras\Index as ComprasIndex;
use App\Livewire\Contas\Index as ContasIndex;
use App\Livewire\Dashboard\Overview;
use App\Livewire\Estoque\Index as EstoqueIndex;
use App\Livewire\FichaTecnica\Index as FichaTecnicaIndex;
use App\Livewire\Fornecedores\Index as FornecedoresIndex;
use App\Livewire\Vendas\Index as VendasIndex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard',     Overview::class)->name('dashboard');

    // Rotas futuras — retornam 404 até serem implementadas
    Route::get('/vendas',        VendasIndex::class)->name('vendas');
    Route::get('/compras',       ComprasIndex::class)->name('compras');
    Route::get('/estoque',       EstoqueIndex::class)->name('estoque');
    // Route::get('/insumos',       fn() => abort(404))->name('insumos');
    Route::get('/ficha-tecnica', FichaTecnicaIndex::class)->name('ficha-tecnica');
    Route::get('/clientes',      ClientesIndex::class)->name('clientes');
    Route::get('/fornecedores',  FornecedoresIndex::class)->name('fornecedores');
    Route::get('/contas',        ContasIndex::class)->name('contas');
});
