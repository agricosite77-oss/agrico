<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class UserEditModal extends Component
{
    use WithFileUploads;

    public $showModal = false;
    public $isVerified = false;

    public $userId = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = 'member';
    public $student_id = '';
    public $section = '';
    public $status = 'Active';
    public $photo;

    public $adminPassword = '';
    public $adminPasswordError = '';
    public $userRoleName = 'User';

    #[\Livewire\Attributes\On('edit-user')]
    public function openModal($id = null)
    {
        if (is_array($id)) {
            $id = $id['id'] ?? (reset($id) ?: null);
        }

        $user = User::find($id);
        if ($user) {
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->password = '';
            $this->password_confirmation = '';
            $this->role = $user->role ?: 'member';
            $this->student_id = $user->student_id ?: '';
            $this->section = $user->section ?: '';
            $this->status = (ucfirst($user->status) === 'Suspended' || strtolower($user->status) === 'inactive') ? 'Suspended' : 'Active';
            
            $this->userRoleName = ucfirst($user->role ?: 'User');
            $this->adminPassword = '';
            $this->adminPasswordError = '';
            $this->isVerified = false;
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->isVerified = false;
        $this->reset(['userId', 'name', 'email', 'password', 'password_confirmation', 'role', 'student_id', 'section', 'status', 'photo', 'adminPassword', 'adminPasswordError']);
    }

    public function verifyAdminPassword()
    {
        $this->validate([
            'adminPassword' => 'required|string',
        ]);

        $passwordMatches = false;

        $currentUser = Auth::guard('admin')->user();
        if ($currentUser && !empty($currentUser->email)) {
            $adminRecord = \App\Models\Admin::where('email', $currentUser->email)->first();
            if ($adminRecord && $adminRecord->password && Hash::check($this->adminPassword, $adminRecord->password)) {
                $passwordMatches = true;
            }
        }

        if (!$passwordMatches) {
            $admins = \App\Models\Admin::all();
            foreach ($admins as $admin) {
                if ($admin->password && Hash::check($this->adminPassword, $admin->password)) {
                    $passwordMatches = true;
                    break;
                }
            }
        }

        if ($passwordMatches) {
            $this->isVerified = true;
            $this->adminPasswordError = '';
        } else {
            $this->adminPasswordError = 'Incorrect admin password. Authorization denied.';
        }
    }

    public function isSuperAdminActor(): bool
    {
        $authAdmin = Auth::guard('admin')->user();
        if ($authAdmin) {
            $email = strtolower(trim($authAdmin->email ?? $authAdmin->Email ?? ''));
            if ($email === 'admin@agrico.com' || $email === 'agricosite77@gmail.com') {
                return true;
            }
        }

        $webUser = Auth::user();
        if ($webUser) {
            $email = strtolower(trim($webUser->email ?? ''));
            $role = strtolower(trim($webUser->role ?? ''));
            if ($email === 'admin@agrico.com' || $email === 'agricosite77@gmail.com' || $role === 'superadmin') {
                return true;
            }
        }

        if (!empty($authAdmin?->email ?? '')) {
            $userRec = User::whereRaw('LOWER(email) = ?', [strtolower(trim($authAdmin->email))])->first();
            if ($userRec && (strtolower(trim($userRec->role)) === 'superadmin' || strtolower(trim($userRec->email)) === 'admin@agrico.com' || strtolower(trim($userRec->email)) === 'agricosite77@gmail.com')) {
                return true;
            }
        }

        return false;
    }

    public function save()
    {
        if (!$this->isVerified) {
            $this->adminPasswordError = 'Please verify your admin password first.';
            return;
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->userId, 'user_id'),
            ],
            'role' => 'required|string',
            'status' => 'required|string',
            'photo' => 'nullable|image|max:2048',
        ];

        if (strtolower($this->role) === 'student') {
            $rules['student_id'] = 'required|string|max:50';
            $rules['section'] = 'nullable|string|max:100';
        }

        if (!empty($this->password)) {
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        $this->validate($rules);

        if ($this->userId) {
            $user = User::find($this->userId);
            if ($user) {
                $currentLowerRole = strtolower(trim($user->role));
                $newLowerRole = strtolower(trim($this->role));

                if ($currentLowerRole !== $newLowerRole && !$this->isSuperAdminActor()) {
                    $this->addError('role', 'Only the Super Admin can change user roles.');
                    return;
                }

                if (strtolower(trim($user->email)) === 'admin@agrico.com' && $newLowerRole !== 'superadmin') {
                    $this->addError('role', 'The main Super Admin role cannot be changed.');
                    return;
                }

                $user->name = $this->name;
                $user->email = $this->email;
                $user->status = $this->status;

                if (strtolower($this->role) === 'student') {
                    $user->student_id = trim($this->student_id) ?: null;
                    $user->section = trim($this->section) ?: null;
                }

                if ($this->photo) {
                    $user->profile_photo_path = $this->photo->store('profile-photos', 'public');
                }

                if (!empty($this->password)) {
                    $user->password = Hash::make($this->password);
                }

                $user->syncRoleAndAdminId($this->role);

                $user->save();
                $this->dispatch('user-updated');
            }
        }

        $this->closeModal();
    }

    public function render()
    {
        $user = $this->userId ? User::find($this->userId) : null;
        $profilePhotoUrl = $user?->profile_photo_url;
        if ($this->photo) {
            $profilePhotoUrl = $this->photo->temporaryUrl();
        }

        $userInitial = strtoupper(substr($this->name ?: 'U', 0, 1));

        return view('admin.livewire.users.user-edit-modal', [
            'profilePhotoUrl' => $profilePhotoUrl,
            'userInitial' => $userInitial,
        ]);
    }
}
