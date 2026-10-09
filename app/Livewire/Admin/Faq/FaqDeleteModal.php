<?php

namespace App\Livewire\Admin\Faq;

use App\Models\FAQ;
use Livewire\Component;

class FaqDeleteModal extends Component
{
    public $showModal = false;
    public $faqId = null;

    protected $listeners = [
        'delete-faq' => 'openModal',
    ];

    public function openModal($id)
    {
        $this->faqId = $id;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->faqId = null;
    }

    public function delete()
    {
        $faq = FAQ::find($this->faqId);

        if ($faq) {
            $faq->delete();
            $this->dispatch('faq-deleted');
        }

        $this->closeModal();
    }

    public function render()
    {
        return view('admin.livewire.faq.faq-delete-modal');
    }
}
