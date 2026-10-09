<?php

namespace App\Livewire\Admin\Faq;

use App\Models\FAQ;
use Livewire\Component;

class FaqCreateModal extends Component
{
    public $showModal = false;
    public $question = '';
    public $category = 'General';
    public $answer = '';
    public $status = 'Active';
    public $admin_id = '';

    protected $listeners = [
        'create-faq' => 'openModal',
    ];

    protected $rules = [
        'question' => 'required|string|max:255',
        'category' => 'required|string|max:100',
        'answer'   => 'required|string|max:65000',
        'status'   => 'required|in:Active,Inactive',
        'admin_id' => 'nullable|string|max:50',
    ];

    public function openModal()
    {
        $this->reset(['question', 'answer', 'status', 'admin_id']);
        $this->category = 'General';
        $this->status = 'Active';

        $user = auth()->user();
        $adminId = $user?->admin_id ?? $user?->user_id;
        if (!$adminId && $user) {
            $userEmail = $user->email;
            if ($userEmail) {
                $adminRecord = \App\Models\Admin::where('email', $userEmail)->first();
                $adminId = $adminRecord?->admin_id;
            }
        }

        $this->admin_id = \App\Models\Admin::formatId($adminId ?: 'ADM-001');
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['question', 'category', 'answer', 'status', 'admin_id']);
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate();

        $user = auth()->user();
        $adminIdToSave = $user?->Admin_ID ?: $this->admin_id;
        if (empty($adminIdToSave)) {
            $firstAdmin = \App\Models\Admin::first();
            $adminIdToSave = $firstAdmin ? $firstAdmin->Admin_ID : 'ADM001';
        }

        FAQ::create([
            'question' => $this->question,
            'category' => $this->category,
            'answer'   => $this->answer,
            'status'   => $this->status,
            'admin_id' => $adminIdToSave,
        ]);

        $this->closeModal();
        $this->dispatch('faq-saved');
    }

    public function render()
    {
        $existingCategories = FAQ::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->toArray();

        $defaultCategories = ['General', 'Soil Analysis', 'Scanning', 'Mobile App', 'Account & Security'];
        $categories = array_values(array_unique(array_merge($defaultCategories, $existingCategories)));
        $admins = \App\Models\Admin::all();

        return view('admin.livewire.faq.faq-create-modal', [
            'availableCategories' => $categories,
            'admins' => $admins,
        ]);
    }
}
