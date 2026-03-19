<?php

namespace App\Livewire\Pages\Configuracion;

use Livewire\Component;
use App\Models\Configuracion;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component
{
    // Propiedades del formulario
    public ?int $config_id = null;
    public string $descripcion = '';
    public string $valor = '';

    // Estado de la UI
    public bool $showModal = false;
    public bool $editMode = false;

    // Abrir modal para crear o editar
    public function openModal($id = null)
    {
        $this->resetForm();

        if ($id) {
            $config = Configuracion::findOrFail($id);
            $this->config_id = $config->id;
            $this->descripcion = $config->descripcion;
            $this->valor = $config->valor;
            $this->editMode = true;
        }
        
        $this->showModal = true;
    }

    // Guardar o Actualizar
    public function save()
    {
        $this->validate([
            'descripcion' => 'required|string|max:255',
            'valor' => 'required|string',
        ]);

        if ($this->editMode && $this->config_id) {
            $config = Configuracion::findOrFail($this->config_id);
            $config->update([
                'descripcion' => $this->descripcion,
                'valor' => $this->valor,
            ]);
        } else {
            Configuracion::create([
                'descripcion' => $this->descripcion,
                'valor' => $this->valor,
                'user_id' => auth()->id(), // El usuario logueado es el dueño
            ]);
        }

        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->config_id = null;
        $this->descripcion = '';
        $this->valor = '';
        $this->editMode = false;
    }

    public function delete($id)
    {
        Configuracion::findOrFail($id)->delete();
    }
};
?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl">Configuraciones</flux:heading>
            <flux:subheading>Gestione los parámetros globales (IVA, Utilidad, etc.)</flux:subheading>
        </div>
        <flux:button wire:click="openModal" variant="primary" icon="plus">Nueva Configuración</flux:button>
    </div>

    {{-- Tabla de Configuraciones --}}
    <flux:table>
        <flux:table.columns>
            <flux:table.column>ID</flux:table.column>
            <flux:table.column>Descripción / Parámetro</flux:table.column>
            <flux:table.column>Valor</flux:table.column>
            <flux:table.column>Creado por</flux:table.column>
            <flux:table.column align="end">Acciones</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse(\App\Models\Configuracion::latest()->get() as $config)
                <flux:table.row :key="$config->id">
                    <flux:table.cell>{{ $config->id }}</flux:table.cell>
                    <flux:table.cell class="font-medium">{{ strtoupper(str_replace('_', ' ', $config->descripcion)) }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="zinc" inset="top bottom">{{ $config->valor }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $config->user->name }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex gap-2 justify-end">
                            <flux:button size="sm" variant="ghost" icon="pencil-square" wire:click="openModal({{ $config->id }})" />
                            <flux:button size="sm" variant="ghost" icon="trash" color="danger" wire:click="delete({{ $config->id }})" />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center py-10 text-zinc-400">
                        No hay configuraciones registradas.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- Modal de Edición/Creación --}}
    <flux:modal wire:model="showModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editMode ? 'Editar Parámetro' : 'Nuevo Parámetro' }}</flux:heading>
                <flux:subheading>Defina el nombre y valor del ajuste.</flux:subheading>
            </div>

            <form wire:submit="save" class="space-y-4">
                <flux:input 
                    label="Descripción" 
                    placeholder="Ej: iva_porcentaje" 
                    wire:model="descripcion" 
                />

                <flux:input 
                    label="Valor" 
                    placeholder="Ej: 16" 
                    wire:model="valor" 
                />

                <div class="flex gap-2 justify-end">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancelar</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">{{ $editMode ? 'Actualizar' : 'Guardar' }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>