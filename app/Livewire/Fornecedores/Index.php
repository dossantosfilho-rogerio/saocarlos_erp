<?php

namespace App\Livewire\Fornecedores;

use App\Models\Fornecedor;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public ?int $editingId = null;
    public bool $showFormModal = false;

    public string $search = '';

    public array $form = [
        'tipo' => 'PJ',
        'nome' => '',
        'documento' => '',
        'email' => '',
        'telefone' => '',
        'cidade' => '',
        'uf' => '',
        'endereco' => '',
        'ativo' => true,
        'observacoes' => '',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'form.tipo' => ['required', Rule::in(['PF', 'PJ'])],
            'form.nome' => ['required', 'string', 'max:255'],
            'form.documento' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('fornecedores', 'documento')->ignore($this->editingId),
            ],
            'form.email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('fornecedores', 'email')->ignore($this->editingId),
            ],
            'form.telefone' => ['nullable', 'string', 'max:20'],
            'form.cidade' => ['nullable', 'string', 'max:120'],
            'form.uf' => ['nullable', 'string', 'size:2'],
            'form.endereco' => ['nullable', 'string', 'max:255'],
            'form.ativo' => ['boolean'],
            'form.observacoes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate()['form'];
        $validated['uf'] = strtoupper((string) $validated['uf']);

        if ($this->editingId) {
            Fornecedor::query()->findOrFail($this->editingId)->update($validated);
            session()->flash('status', 'Fornecedor atualizado com sucesso.');
        } else {
            Fornecedor::query()->create($validated);
            session()->flash('status', 'Fornecedor criado com sucesso.');
        }

        $this->cancelEdit();
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->cancelEdit();
        $this->showFormModal = true;
    }

    public function edit(int $id): void
    {
        $fornecedor = Fornecedor::query()->findOrFail($id);

        $this->editingId = $fornecedor->id;
        $this->form = [
            'tipo' => $fornecedor->tipo,
            'nome' => $fornecedor->nome,
            'documento' => $fornecedor->documento ?? '',
            'email' => $fornecedor->email ?? '',
            'telefone' => $fornecedor->telefone ?? '',
            'cidade' => $fornecedor->cidade ?? '',
            'uf' => $fornecedor->uf ?? '',
            'endereco' => $fornecedor->endereco ?? '',
            'ativo' => (bool) $fornecedor->ativo,
            'observacoes' => $fornecedor->observacoes ?? '',
        ];

        $this->showFormModal = true;
    }

    public function cancelEdit(): void
    {
        $this->showFormModal = false;
        $this->editingId = null;
        $this->reset('form');
        $this->form['tipo'] = 'PJ';
        $this->form['ativo'] = true;
        $this->resetValidation();
    }

    public function delete(int $id): void
    {
        Fornecedor::query()->whereKey($id)->delete();
        session()->flash('status', 'Fornecedor removido com sucesso.');

        if ($this->editingId === $id) {
            $this->cancelEdit();
        }

        $this->resetPage();
    }

    public function render()
    {
        $fornecedores = Fornecedor::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('nome', 'like', '%' . $this->search . '%')
                        ->orWhere('documento', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('telefone', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.fornecedores.index', [
            'fornecedores' => $fornecedores,
        ]);
    }
}
