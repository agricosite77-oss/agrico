<?php

namespace App\Livewire\Admin\MunsellColor;

use App\Models\MunsellColor;
use Livewire\Component;
use Livewire\WithFileUploads;

class MunsellFormModal extends Component
{
    use WithFileUploads;

    public $showModal = false;
    public $colorId = null;
    public $hue = '';
    public $value = '';
    public $chroma = '';
    public $full_code = '';
    public $color_name = '';
    public $description = '';
    public $status = 'Active';
    public $admin_id = '';
    public $image = null;
    public $existingImage = null;

    protected $listeners = [];

    protected $rules = [
        'hue'        => 'required|string|max:20',
        'value'      => 'required|numeric|min:0|max:10',
        'chroma'     => 'required|numeric|min:0|max:20',
        'full_code'  => 'required|string|max:30',
        'color_name' => 'nullable|string|max:100',
        'description'=> 'nullable|string|max:500',
        'status'     => 'required|in:Active,Inactive',
        'admin_id'   => 'nullable|string|max:50',
        'image'      => 'nullable|image|max:10240',
    ];

    public function openCreateModal()
    {
        $this->reset(['colorId', 'hue', 'value', 'chroma', 'full_code', 'color_name', 'description', 'status', 'admin_id', 'image', 'existingImage']);
        $this->status = 'Active';
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openModal($id)
    {
        $color = MunsellColor::find($id);

        if (! $color) {
            return;
        }

        $this->colorId   = $color->id;
        $this->hue       = $color->hue;
        $this->value     = $color->value;
        $this->chroma    = $color->chroma;
        $this->full_code = $color->full_code;
        $this->color_name  = $color->color_name ?? '';
        $this->description = $color->description ?? '';
        $this->status      = $color->status ?? 'Active';
        $this->admin_id    = $color->admin_id ?? '';
        $this->existingImage = $color->image ?? null;
        $this->image = null;

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['colorId', 'hue', 'value', 'chroma', 'full_code', 'color_name', 'description', 'status', 'admin_id', 'image', 'existingImage']);
        $this->resetErrorBag();
    }

    public function updatedHue()
    {
        $this->updateFullCode();
    }

    public function updatedValue()
    {
        $this->updateFullCode();
    }

    public function updatedChroma()
    {
        $this->updateFullCode();
    }

    private function updateFullCode()
    {
        if ($this->hue && $this->value !== '' && $this->chroma !== '') {
            $this->full_code = trim($this->hue . ' ' . $this->value . '/' . $this->chroma);
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'hue'        => $this->hue,
            'value'      => $this->value,
            'chroma'     => $this->chroma,
            'full_code'  => $this->full_code,
            'color_name' => $this->color_name,
            'description'=> $this->description,
            'status'     => $this->status,
            'admin_id'   => $this->admin_id,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('munsell_colors', 'public');
        }

        if ($this->colorId) {
            $color = MunsellColor::find($this->colorId);
            if ($color) {
                $color->update($data);
            }
        } else {
            MunsellColor::create($data);
        }

        $this->closeModal();

        $this->dispatch('munsell-saved');
    }

    public function render()
    {
        $munsellHues = MunsellColor::select('hue')
            ->distinct()
            ->whereNotNull('hue')
            ->orderBy('hue')
            ->pluck('hue')
            ->toArray();

        $defaultHues = ['10R', '2.5YR', '5YR', '7.5YR', '10YR', '2.5Y'];

        $hues = array_values(array_unique(array_merge($defaultHues, $munsellHues)));

        return view('admin.livewire.munsell-color.munsell-form-modal', [
            'availableHues' => $hues,
        ]);
    }
}
