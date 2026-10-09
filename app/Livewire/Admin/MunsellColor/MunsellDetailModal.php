<?php

namespace App\Livewire\Admin\MunsellColor;

use App\Models\MunsellColor;
use Livewire\Component;

class MunsellDetailModal extends Component
{
    public $showModal = false;
    public ?MunsellColor $color = null;

    protected $listeners = [
        'view-munsell' => 'openModal',
    ];

    public function openModal($id)
    {
        $this->color = MunsellColor::find($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->color = null;
    }

    public function edit()
    {
        $id = $this->color?->id;
        $this->closeModal();
        $this->dispatch('edit-munsell', id: $id);
    }

    public function render()
    {
        return view('admin.livewire.munsell-color.munsell-detail-modal');
    }
}
