<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class UserSuspendModal extends Component
{
    public $showModal = false;
    public $userId = null;
    public $user = null;

    #[\Livewire\Attributes\On('suspend-user')]
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

    public function confirmSuspend()
    {
        if ($this->userId) {
            $user = User::find($this->userId);
            if ($user) {
                $user->status = ($user->status === 'Suspended') ? 'Active' : 'Suspended';
                $user->save();
                $this->dispatch('user-updated');
            }
        }
        $this->closeModal();
    }

    public function render()
    {
        return view('admin.livewire.users.user-suspend-modal');
    }
}
