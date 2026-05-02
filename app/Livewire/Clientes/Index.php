<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
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
        'inscricao_estadual' => '',
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
                Rule::unique('clientes', 'documento')->ignore($this->editingId),
            ],
            'form.inscricao_estadual' => ['nullable', 'string', 'max:30'],
            'form.email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('clientes', 'email')->ignore($this->editingId),
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
        $validated['inscricao_estadual'] = $validated['tipo'] === 'PJ'
            ? ($validated['inscricao_estadual'] !== '' ? $validated['inscricao_estadual'] : null)
            : null;

        if ($this->editingId) {
            Cliente::query()->findOrFail($this->editingId)->update($validated);
            session()->flash('status', 'Cliente atualizado com sucesso.');
        } else {
            Cliente::query()->create($validated);
            session()->flash('status', 'Cliente criado com sucesso.');
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
        $cliente = Cliente::query()->findOrFail($id);

        $this->editingId = $cliente->id;
        $this->form = [
            'tipo' => $cliente->tipo,
            'nome' => $cliente->nome,
            'documento' => $cliente->documento ?? '',
            'inscricao_estadual' => $cliente->inscricao_estadual ?? '',
            'email' => $cliente->email ?? '',
            'telefone' => $cliente->telefone ?? '',
            'cidade' => $cliente->cidade ?? '',
            'uf' => $cliente->uf ?? '',
            'endereco' => $cliente->endereco ?? '',
            'ativo' => (bool) $cliente->ativo,
            'observacoes' => $cliente->observacoes ?? '',
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
        Cliente::query()->whereKey($id)->delete();
        session()->flash('status', 'Cliente removido com sucesso.');

        if ($this->editingId === $id) {
            $this->cancelEdit();
        }

        $this->resetPage();
    }

    public function render()
    {
        $clientes = Cliente::query()
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

        return view('livewire.clientes.index', [
            'clientes' => $clientes,
        ]);
    }
}
