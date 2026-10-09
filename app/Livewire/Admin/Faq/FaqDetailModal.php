<?php

namespace App\Livewire\Admin\Faq;

use App\Models\FAQ;
use Livewire\Component;

class FaqDetailModal extends Component
{
    public $showModal = false;
    public ?FAQ $faq = null;

    protected $listeners = [
        'view-faq' => 'openModal',
    ];

    public function openModal($id)
    {
        $this->faq = FAQ::find($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->faq = null;
    }

    public function edit()
    {
        $id = $this->faq?->id;
        $this->closeModal();
        $this->dispatch('edit-faq', id: $id);
    }

    public function render()
    {
        return view('admin.livewire.faq.faq-detail-modal');
    }
}
