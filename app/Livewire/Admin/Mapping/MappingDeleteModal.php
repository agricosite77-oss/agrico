<?php

namespace App\Livewire\Admin\Mapping;

use App\Models\LocationMapping;
use Livewire\Component;

class MappingDeleteModal extends Component
{
    public $showModal = false;
    public $mappingId = null;
    public $mapping = null;

    protected $listeners = [
        'delete-mapping' => 'openModal',
    ];

    public function openModal($id)
    {
        if (is_array($id)) {
            $id = $id['id'] ?? ($id[0] ?? null);
        }
        $id = ltrim((string) $id, '#');
        $this->mappingId = $id;
        $this->mapping = LocationMapping::with('soil')->find($id);
        if ($this->mapping) {
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->mappingId = null;
        $this->mapping = null;
    }

    public function confirmDelete()
    {
        if ($this->mappingId) {
            $mapping = LocationMapping::find($this->mappingId);
            if ($mapping) {
                $mapping->delete();
                $this->dispatch('mapping-deleted');
            }
        }
        $this->closeModal();
    }

    public function render()
    {
        return view('admin.livewire.mapping.mapping-delete-modal');
    }
}
