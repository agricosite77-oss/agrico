<?php

namespace App\Livewire\Admin;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class AdminProfileModal extends Component
{
    use WithFileUploads;

    public bool $showModal = false;
    public bool $isEditing = false;

    public string $name = '';
    public string $email = '';
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';
    public $photo;

    public bool $removePhoto = false;

    protected $listeners = [
        'open-admin-profile' => 'openModal',
    ];

    public function mount()
    {
        $this->loadUserData();
    }

    public function loadUserData()
    {
        $user = auth()->user() ?? Auth::guard('admin')->user();
        if ($user) {
            $this->name = $user->name ?? $user->Full_Name ?? $user->full_name ?? '';
            $this->email = $user->email ?? $user->Email ?? '';
        }
    }

    public function openModal()
    {
        $this->loadUserData();
        $this->isEditing = false;
        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->photo = null;
        $this->removePhoto = false;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->isEditing = false;
    }

    public function toggleEdit()
    {
        $this->loadUserData();
        $this->isEditing = !$this->isEditing;
    }

    public function deletePhoto()
    {
        $this->photo = null;
        $this->removePhoto = true;
    }

    public function save()
    {
        $user = auth()->user() ?? Auth::guard('admin')->user();
        if (!$user) return;

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'photo' => 'nullable|image|max:2048',
        ];

        if (!empty($this->password)) {
            $rules['password'] = 'required|string|min:8|same:password_confirmation';
        }

        $this->validate($rules);

        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('profile-photos', 'public');
            $this->removePhoto = false;
        }

        // Update Admin model if authenticated as Admin
        if ($user instanceof Admin) {
            $updateData = [
                'full_name' => $this->name,
                'email' => $this->email,
            ];

            if ($this->removePhoto) {
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }
                $updateData['profile_photo_path'] = null;
            } elseif ($photoPath) {
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }
                $updateData['profile_photo_path'] = $photoPath;
            }

            if (!empty($this->password)) {
                $updateData['password'] = Hash::make($this->password);
            }
            $user->update($updateData);

            // Sync matching User table record if present
            $matchingUser = User::where('email', $this->email)->orWhere('last_admin_id', $user->admin_id)->first();
            if ($matchingUser) {
                $userSyncData = ['name' => $this->name, 'email' => $this->email];
                if ($this->removePhoto) {
                    if ($matchingUser->profile_photo_path) Storage::disk('public')->delete($matchingUser->profile_photo_path);
                    $userSyncData['profile_photo_path'] = null;
                } elseif ($photoPath) {
                    $userSyncData['profile_photo_path'] = $photoPath;
                }
                if (!empty($this->password)) $userSyncData['password'] = Hash::make($this->password);
                $matchingUser->update($userSyncData);
            }
        } elseif ($user instanceof User) {
            $updateData = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            if ($this->removePhoto) {
                if ($user->profile_photo_path) Storage::disk('public')->delete($user->profile_photo_path);
                $updateData['profile_photo_path'] = null;
            } elseif ($photoPath) {
                if ($user->profile_photo_path) Storage::disk('public')->delete($user->profile_photo_path);
                $updateData['profile_photo_path'] = $photoPath;
            }

            if (!empty($this->password)) {
                $updateData['password'] = Hash::make($this->password);
            }
            $user->update($updateData);

            // Sync matching Admin table record if present
            $adminId = $user->admin_id ?? $user->user_id;
            $matchingAdmin = Admin::where('email', $this->email)->orWhere('admin_id', $adminId)->first();
            if ($matchingAdmin) {
                $adminSyncData = ['full_name' => $this->name, 'email' => $this->email];
                if ($this->removePhoto) {
                    if ($matchingAdmin->profile_photo_path) Storage::disk('public')->delete($matchingAdmin->profile_photo_path);
                    $adminSyncData['profile_photo_path'] = null;
                } elseif ($photoPath) {
                    $adminSyncData['profile_photo_path'] = $photoPath;
                }
                if (!empty($this->password)) $adminSyncData['password'] = Hash::make($this->password);
                $matchingAdmin->update($adminSyncData);
            }
        }

        $user->name = $this->name;
        if (isset($user->Full_Name)) {
            $user->Full_Name = $this->name;
        }
        if (isset($user->full_name)) {
            $user->full_name = $this->name;
        }

        $user->email = $this->email;
        if (isset($user->Email)) {
            $user->Email = $this->email;
        }

        if ($this->password) {
            $user->password = Hash::make($this->password);
        }

        if ($this->removePhoto) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
                $user->profile_photo_path = null;
            }
        } elseif ($this->photo) {
            $path = $this->photo->store('profile-photos', 'public');
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = $path;
        }

        $user->save();

        session()->flash('message', 'Profile updated successfully.');
        $this->isEditing = false;
        $this->photo = null;
    }

    public function render()
    {
        $user = auth()->user() ?? Auth::guard('admin')->user();

        $adminName = $user?->name ?: ($user?->Full_Name ?: ($user?->full_name ?: 'AgriCo User'));
        if ($this->isEditing && $this->name) {
            $adminName = $this->name;
        }

        $adminEmail = $user?->email ?: ($user?->Email ?: '');
        if ($this->isEditing && $this->email) {
            $adminEmail = $this->email;
        }

        $adminId = $user?->admin_id ?? $user?->user_id ?? $user?->id ?? 'ADM-001';
        $rawRole = $user?->role ?? 'Admin';
        $adminRole = (in_array(strtolower($rawRole), ['superadmin', 'super admin'])) ? 'Super Admin' : ucfirst($rawRole);
        $adminInitial = strtoupper(substr($adminName, 0, 1));
        
        $profilePhotoUrl = $user?->profile_photo_url;
        if ($this->removePhoto) {
            $profilePhotoUrl = null;
        } elseif ($this->photo) {
            $profilePhotoUrl = $this->photo->temporaryUrl();
        }

        $joinedDate = '04 Aug 2026';
        if ($user) {
            $created = $user->created_at ?? null;
            if (!$created) {
                $matchingUser = User::where('email', $adminEmail)->first();
                $created = $matchingUser?->created_at;
            }
            if ($created) {
                $joinedDate = $created->format('d M Y');
            }
        }

        return view('admin.livewire.admin.admin-profile-modal', [
            'adminUser' => $user,
            'adminName' => $adminName,
            'adminEmail' => $adminEmail,
            'adminId' => $adminId,
            'adminRole' => $adminRole,
            'adminInitial' => $adminInitial,
            'profilePhotoUrl' => $profilePhotoUrl,
            'joinedDate' => $joinedDate,
        ]);
    }
}
