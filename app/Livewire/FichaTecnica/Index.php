<?php

namespace App\Livewire\FichaTecnica;

use App\Models\Insumo;
use App\Models\Produto;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public ?int $editingProdutoId = null;
    public ?int $editingInsumoId = null;

    public bool $showProdutoModal = false;
    public bool $showInsumoModal = false;

    public string $searchProduto = '';
    public string $searchInsumo = '';

    public array $produtoForm = [
        'nome' => '',
        'sku' => '',
        'unidade_padrao' => 'kg',
        'ativo' => true,
        'observacoes' => '',
    ];

    public array $insumoForm = [
        'nome' => '',
        'codigo' => '',
        'unidade' => 'kg',
        'ativo' => true,
        'observacoes' => '',
    ];

    public function updatedSearchProduto(): void
    {
        $this->resetPage('produtosPage');
    }

    public function updatedSearchInsumo(): void
    {
        $this->resetPage('insumosPage');
    }

    protected function produtoRules(): array
    {
        return [
            'produtoForm.nome' => ['required', 'string', 'max:255'],
            'produtoForm.sku' => [
                'nullable',
                'string',
                'max:40',
                Rule::unique('produtos', 'sku')->ignore($this->editingProdutoId),
            ],
            'produtoForm.unidade_padrao' => ['required', 'string', 'max:10'],
            'produtoForm.ativo' => ['boolean'],
            'produtoForm.observacoes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function insumoRules(): array
    {
        return [
            'insumoForm.nome' => ['required', 'string', 'max:255'],
            'insumoForm.codigo' => [
                'nullable',
                'string',
                'max:40',
                Rule::unique('insumos', 'codigo')->ignore($this->editingInsumoId),
            ],
            'insumoForm.unidade' => ['required', 'string', 'max:10'],
            'insumoForm.ativo' => ['boolean'],
            'insumoForm.observacoes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function openProdutoModal(): void
    {
        $this->cancelProduto();
        $this->showProdutoModal = true;
    }

    public function saveProduto(): void
    {
        $validated = $this->validate($this->produtoRules())['produtoForm'];
        $validated['sku'] = $validated['sku'] !== '' ? $validated['sku'] : null;

        if ($this->editingProdutoId) {
            Produto::query()->findOrFail($this->editingProdutoId)->update($validated);
            session()->flash('status', 'Produto atualizado com sucesso.');
        } else {
            Produto::query()->create($validated);
            session()->flash('status', 'Produto criado com sucesso.');
        }

        $this->cancelProduto();
        $this->resetPage('produtosPage');
    }

    public function editProduto(int $id): void
    {
        $produto = Produto::query()->findOrFail($id);

        $this->editingProdutoId = $produto->id;
        $this->produtoForm = [
            'nome' => $produto->nome,
            'sku' => $produto->sku ?? '',
            'unidade_padrao' => $produto->unidade_padrao,
            'ativo' => (bool) $produto->ativo,
            'observacoes' => $produto->observacoes ?? '',
        ];

        $this->showProdutoModal = true;
    }

    public function cancelProduto(): void
    {
        $this->showProdutoModal = false;
        $this->editingProdutoId = null;
        $this->reset('produtoForm');
        $this->produtoForm['unidade_padrao'] = 'kg';
        $this->produtoForm['ativo'] = true;
        $this->resetValidation();
    }

    public function deleteProduto(int $id): void
    {
        Produto::query()->whereKey($id)->delete();
        session()->flash('status', 'Produto removido com sucesso.');

        $this->resetPage('produtosPage');
    }

    public function openInsumoModal(): void
    {
        $this->cancelInsumo();
        $this->showInsumoModal = true;
    }

    public function saveInsumo(): void
    {
        $validated = $this->validate($this->insumoRules())['insumoForm'];
        $validated['codigo'] = $validated['codigo'] !== '' ? $validated['codigo'] : null;

        if ($this->editingInsumoId) {
            Insumo::query()->findOrFail($this->editingInsumoId)->update($validated);
            session()->flash('status', 'Insumo atualizado com sucesso.');
        } else {
            // Custo unitario sera alimentado automaticamente quando o modulo de entradas for implementado.
            Insumo::query()->create([...$validated, 'custo_unitario' => 0]);
            session()->flash('status', 'Insumo criado com sucesso.');
        }

        $this->cancelInsumo();
        $this->resetPage('insumosPage');
    }

    public function editInsumo(int $id): void
    {
        $insumo = Insumo::query()->findOrFail($id);

        $this->editingInsumoId = $insumo->id;
        $this->insumoForm = [
            'nome' => $insumo->nome,
            'codigo' => $insumo->codigo ?? '',
            'unidade' => $insumo->unidade,
            'ativo' => (bool) $insumo->ativo,
            'observacoes' => $insumo->observacoes ?? '',
        ];

        $this->showInsumoModal = true;
    }

    public function cancelInsumo(): void
    {
        $this->showInsumoModal = false;
        $this->editingInsumoId = null;
        $this->reset('insumoForm');
        $this->insumoForm['unidade'] = 'kg';
        $this->insumoForm['ativo'] = true;
        $this->resetValidation();
    }

    public function deleteInsumo(int $id): void
    {
        Insumo::query()->whereKey($id)->delete();
        session()->flash('status', 'Insumo removido com sucesso.');
        $this->resetPage('insumosPage');
    }

    public function render()
    {
        $produtos = Produto::query()
            ->when($this->searchProduto !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('nome', 'like', '%' . $this->searchProduto . '%')
                        ->orWhere('sku', 'like', '%' . $this->searchProduto . '%');
                });
            })
            ->orderByDesc('id')
            ->paginate(8, ['*'], 'produtosPage');

        $insumos = Insumo::query()
            ->when($this->searchInsumo !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('nome', 'like', '%' . $this->searchInsumo . '%')
                        ->orWhere('codigo', 'like', '%' . $this->searchInsumo . '%');
                });
            })
            ->orderByDesc('id')
            ->paginate(8, ['*'], 'insumosPage');

        return view('livewire.ficha-tecnica.index', [
            'produtos' => $produtos,
            'insumos' => $insumos,
        ]);
    }
}
