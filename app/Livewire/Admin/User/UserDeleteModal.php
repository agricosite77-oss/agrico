<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class UserDeleteModal extends Component
{
    public $showModal = false;
    public $userId = null;
    public $user = null;

    #[\Livewire\Attributes\On('delete-user')]
    public function openModal($id = null)
    {
        if (is_array($id)) {
            $id = $id['id'] ?? (reset($id) ?: null);
        }

        $this->userId = $id;
        $this->user = User::find($id);
        if ($this->user) {
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->userId = null;
        $this->user = null;
    }

    public function confirmDelete()
    {
        if ($this->userId) {
            $user = User::find($this->userId);
            if ($user) {
                $user->delete();
                $this->dispatch('user-deleted');
            }
        }
        $this->closeModal();
    }

    public function render()
    {
        return view('admin.livewire.users.user-delete-modal');
    }
}
