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
    public $role = 'user';
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
            $userRole = strtolower(trim($user->role ?: 'user'));
            if ($userRole === 'member') {
                $userRole = 'user';
            }
            $this->role = $userRole;
            $this->student_id = $user->student_id ?: '';
            $this->section = $user->section ?: '';
            $this->status = (ucfirst($user->status) === 'Suspended' || strtolower($user->status) === 'inactive') ? 'Suspended' : 'Active';
            
            $this->userRoleName = in_array($this->role, ['superadmin', 'super admin']) ? 'Super Admin' : ucfirst($this->role);
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
            $adminId = strtoupper(trim($authAdmin->admin_id ?? $authAdmin->Admin_ID ?? ''));
            if ($email === 'admin@agrico.com' || $email === 'agricosite77@gmail.com' || $adminId === 'ADM-001' || $adminId === '1') {
                return true;
            }
        }

        $webUser = Auth::user();
        if ($webUser) {
            $email = strtolower(trim($webUser->email ?? ''));
            $role = strtolower(trim($webUser->role ?? ''));
            if ($email === 'admin@agrico.com' || $email === 'agricosite77@gmail.com' || in_array($role, ['superadmin', 'super admin'])) {
                return true;
            }
        }

        if (!empty($authAdmin?->email ?? '')) {
            $userRec = User::whereRaw('LOWER(email) = ?', [strtolower(trim($authAdmin->email))])->first();
            if ($userRec && (in_array(strtolower(trim($userRec->role)), ['superadmin', 'super admin']) || strtolower(trim($userRec->email)) === 'admin@agrico.com' || strtolower(trim($userRec->email)) === 'agricosite77@gmail.com')) {
                return true;
            }
        }

        return false;
    }

    public function updatedRole(mixed $value): void
    {
        if (is_string($value) && strtolower($value) === 'student' && empty($this->student_id)) {
            $user = $this->userId ? User::find($this->userId) : null;
            if ($user instanceof User) {
                $userIdentifier = (string)($user->user_id ?: $user->id);
                $this->student_id = $user->student_id ?: (preg_match('/^\d{4}-\d+$/', $userIdentifier) ? $userIdentifier : date('Y') . '-' . str_pad($userIdentifier, 5, '0', STR_PAD_LEFT));
            }
        }
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
            'role' => ['required', 'string', Rule::in(['superadmin', 'admin', 'user', 'student'])],
            'status' => 'required|string',
            'photo' => 'nullable|image|max:2048',
        ];

        if (strtolower($this->role) === 'student') {
            $rules['student_id'] = 'nullable|string|max:50';
            $rules['section'] = 'nullable|string|max:100';
        }

        if (!empty($this->password)) {
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        $this->validate($rules);

        if ($this->userId) {
            $user = User::find($this->userId);
            if ($user instanceof User) {
                $currentLowerRole = strtolower(trim($user->role));
                $newLowerRole = strtolower(trim($this->role));

                $adminRoles = ['admin', 'superadmin', 'super admin', 'administrator'];
                $isChangingAdminStatus = in_array($currentLowerRole, $adminRoles) || in_array($newLowerRole, $adminRoles);

                if ($isChangingAdminStatus && !$this->isSuperAdminActor()) {
                    $this->addError('role', 'Only the Super Admin can promote to or demote from Admin.');
                    return;
                }

                if (strtolower(trim($user->email)) === 'admin@agrico.com' && !in_array($newLowerRole, ['superadmin', 'admin'])) {
                    $this->addError('role', 'The main Super Admin role cannot be changed.');
                    return;
                }

                $user->name = $this->name;
                $user->email = $this->email;
                $user->status = $this->status;

                if (strtolower($this->role) === 'student') {
                    $studentId = trim($this->student_id ?: '');
                    if ($studentId === '') {
                        if (!empty($user->student_id)) {
                            $studentId = $user->student_id;
                        } elseif (!empty($user->user_id) && preg_match('/^\d{4}-\d+$/', (string)$user->user_id)) {
                            $studentId = (string)$user->user_id;
                        } else {
                            $userIdentifier = (string)($user->user_id ?: $user->id);
                            $studentId = date('Y') . '-' . str_pad($userIdentifier, 5, '0', STR_PAD_LEFT);
                        }
                    }
                    $user->student_id = $studentId;
                    $user->section = trim($this->section ?: '') ?: null;
                    $user->student_record_status = 'Active';
                } else {
                    $user->student_id = null;
                    $user->section = null;
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
