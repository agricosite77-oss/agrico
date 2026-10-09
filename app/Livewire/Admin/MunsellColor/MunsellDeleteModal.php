<?php

namespace App\Livewire\Admin\MunsellColor;

use App\Models\MunsellColor;
use Livewire\Component;

class MunsellDeleteModal extends Component
{
    public $showModal = false;
    public $colorId = null;

    protected $listeners = [
        'delete-munsell' => 'openModal',
    ];

    public function openModal($id)
    {
        $this->colorId = $id;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->colorId = null;
    }

    public function delete()
    {
        $color = MunsellColor::find($this->colorId);

        if ($color) {
            $color->delete();
            $this->dispatch('munsell-deleted');
        }

        $this->closeModal();
    }

    public function render()
    {
        return view('admin.livewire.munsell-color.munsell-delete-modal');
    }
}
