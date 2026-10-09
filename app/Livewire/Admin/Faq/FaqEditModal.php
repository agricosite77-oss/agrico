<?php

namespace App\Livewire\Admin\Faq;

use App\Models\FAQ;
use Livewire\Component;

class FaqEditModal extends Component
{
    public $showModal = false;
    public $faqId = null;
    public $question = '';
    public $category = 'General';
    public $answer = '';
    public $status = 'Active';
    public $admin_id = '';

    protected $listeners = [
        'edit-faq' => 'openModal',
    ];

    protected function rules()
    {
        return [
            'question' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'answer'   => 'required|string|max:65000',
            'status'   => 'required|in:Active,Inactive',
            'admin_id' => 'nullable|string|max:50',
        ];
    }

    public function openModal($id)
    {
        $faq = FAQ::find($id);

        if (! $faq) {
            return;
        }

        $this->faqId    = $faq->id;
        $this->question = $faq->question;
        $this->category = $faq->category ?? 'General';
        $this->answer   = $faq->answer;
        $this->status   = $faq->status ?? 'Active';

        $user = auth()->user();
        $adminId = $user?->Admin_ID;
        if (!$adminId && $user) {
            $userEmail = $user?->Email ?? $user?->email;
            if ($userEmail) {
                $adminRecord = \App\Models\Admin::where('Email', $userEmail)->first();
                $adminId = $adminRecord?->Admin_ID;
            }
        }

        $this->admin_id = \App\Models\Admin::formatId($adminId ?: ($faq->admin_id ?: 'ADM-001'));
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['faqId', 'question', 'category', 'answer', 'status', 'admin_id']);
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate();

        $faq = FAQ::find($this->faqId);

        if (! $faq) {
            return;
        }

        $user = auth()->user();
        $adminIdToSave = $user?->Admin_ID ?: $this->admin_id;
        if (empty($adminIdToSave)) {
            $firstAdmin = \App\Models\Admin::first();
            $adminIdToSave = $firstAdmin ? $firstAdmin->Admin_ID : 'ADM001';
        }

        $faq->update([
            'question' => $this->question,
            'category' => $this->category,
            'answer'   => $this->answer,
            'status'   => $this->status,
            'admin_id'      => $adminIdToSave,
            'last_admin_id' => $adminIdToSave,
            'updated_at'    => now(),
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

        return view('admin.livewire.faq.faq-edit-modal', [
            'availableCategories' => $categories,
            'admins' => $admins,
        ]);
    }
}
