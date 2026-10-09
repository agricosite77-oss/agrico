<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UserPasswordModal extends Component
{
    public $showModal = false;
    public $isVerified = false;

    public $userId = null;
    public $userName = '';
    public $email = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    public $admin_password = '';
    public $admin_password_error = '';

    protected $listeners = [
        'change-credentials' => 'openModal',
    ];

    public function openModal($id)
    {
        $user = User::find($id);
        if ($user) {
            $this->userId = $user->id;
            $this->userName = $user->name;
            $this->email = $user->email;
            $this->new_password = '';
            $this->new_password_confirmation = '';
            $this->admin_password = '';
            $this->admin_password_error = '';
            $this->isVerified = false;
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset([
            'userId',
            'userName',
            'email',
            'new_password',
            'new_password_confirmation',
            'admin_password',
            'admin_password_error',
            'isVerified'
        ]);
    }

    public function verifyAdminPassword()
    {
        $this->validate([
            'admin_password' => 'required|string',
        ]);

        $passwordMatches = false;

        $currentUser = Auth::guard('admin')->user();
        if ($currentUser && !empty($currentUser->email)) {
            $adminRecord = \App\Models\Admin::where('email', $currentUser->email)->first();
            if ($adminRecord && $adminRecord->password && Hash::check($this->admin_password, $adminRecord->password)) {
                $passwordMatches = true;
            }
        }

        if (!$passwordMatches) {
            $admins = \App\Models\Admin::all();
            foreach ($admins as $admin) {
                if ($admin->password && Hash::check($this->admin_password, $admin->password)) {
                    $passwordMatches = true;
                    break;
                }
            }
        }

        if ($passwordMatches) {
            $this->isVerified = true;
            $this->admin_password_error = '';
        } else {
            $this->admin_password_error = 'Incorrect admin password. Authorization denied.';
        }
    }

    public function save()
    {
        if (! $this->isVerified) {
            $this->admin_password_error = 'Please verify admin password first.';
            return;
        }

        $rules = [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->userId, 'user_id'),
            ],
        ];

        if (! empty($this->new_password)) {
            $rules['new_password'] = 'required|string|min:6|confirmed';
        }

        $this->validate($rules);

        if ($this->userId) {
            $user = User::find($this->userId);
            if ($user) {
                $user->email = $this->email;

                if (! empty($this->new_password)) {
                    $user->password = Hash::make($this->new_password);
                }

                $user->save();
                $this->dispatch('user-updated');
            }
        }

        $this->closeModal();
    }

    public function render()
    {
        return view('admin.livewire.users.user-password-modal');
    }
}
