<?php

namespace App\Livewire\Admin\Mapping;

use App\Models\LocationMapping;
use Livewire\Attributes\On;
use Livewire\Component;

class MappingDetailModal extends Component
{
    public $showModal = false;
    public $mapping = null;

    #[On('view-mapping')]
    public function openModal($id)
    {
        if (is_array($id)) {
            $id = $id['id'] ?? ($id[0] ?? null);
        }
        $id = ltrim((string) $id, '#');
        $this->mapping = LocationMapping::with(['soil.munsellColor', 'soilScan.user'])->find($id);
        if ($this->mapping) {
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->mapping = null;
    }

    public function render()
    {
        return view('admin.livewire.mapping.mapping-detail-modal');
    }
}
