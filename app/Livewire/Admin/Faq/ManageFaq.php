<?php

namespace App\Livewire\Admin\Faq;

use App\Models\FAQ;
use Livewire\Component;
use Livewire\WithPagination;

class ManageFaq extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $listeners = [
        'faq-saved' => '$refresh',
        'faq-deleted' => '$refresh',
    ];

    public function mount()
    {
        if (FAQ::count() === 0) {
            (new \Database\Seeders\FaqSeeder())->run();
        }
    }

    public function syncDefaultFaqs()
    {
        (new \Database\Seeders\FaqSeeder())->run();
        session()->flash('message', 'All 27 standard FAQs have been synchronized with the database successfully.');
        $this->resetPage();
    }

    public function toggleStatus($id)
    {
        $faq = FAQ::find($id);
        if ($faq) {
            $user = auth()->user();
            $adminIdToSave = 'ADM001';
            if ($user && !empty($user->email)) {
                $adminRecord = \App\Models\Admin::where('email', $user->email)->first();
                if ($adminRecord) {
                    $adminIdToSave = $adminRecord->admin_id;
                }
            } else {
                $firstAdmin = \App\Models\Admin::first();
                if ($firstAdmin) {
                    $adminIdToSave = $firstAdmin->admin_id;
                }
            }

            $faq->status = ($faq->status === 'Inactive') ? 'Active' : 'Inactive';
            $faq->admin_id = $adminIdToSave;
            $faq->updated_at = now();
            $faq->save();
        }
    }

    public function sortBy($field)
    {
        $allowedFields = [
            'id',
            'question',
            'category',
            'admin_id',
            'updated_at',
            'created_at',
        ];

        if (! in_array($field, $allowedFields)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategory()
    {
        $this->resetPage();
    }

    public function render()
    {
        $faqs = FAQ::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('question', 'like', '%' . $this->search . '%')
                        ->orWhere('answer', 'like', '%' . $this->search . '%')
                        ->orWhere('category', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->category, function ($query) {
                $query->where('category', $this->category);
            })
            ->orderBy($this->sortField === 'id' ? 'faq_id' : $this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $faqCategories = FAQ::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.livewire.faq.manage-faq', [
            'faqs' => $faqs,
            'faqCategories' => $faqCategories,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
    }
}
