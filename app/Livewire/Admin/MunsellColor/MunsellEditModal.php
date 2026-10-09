<?php

namespace App\Livewire\Admin\MunsellColor;

use App\Models\MunsellColor;
use Livewire\Component;
use Livewire\WithFileUploads;

class MunsellEditModal extends Component
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
    public $hex_code = null;

    protected $listeners = [
        'edit-munsell' => 'openModal',
    ];

    protected function rules()
    {
        return [
            'color_name' => 'nullable|string|max:100',
            'description'=> 'nullable|string|max:65000',
            'hue'        => 'nullable|string|max:20',
            'value'      => 'nullable|numeric|min:0|max:10',
            'chroma'     => 'nullable|numeric|min:0|max:20',
            'full_code'  => 'nullable|string|max:30',
            'status'     => 'nullable|in:Active,Inactive',
            'admin_id'   => 'nullable|string|max:50',
            'image'      => 'nullable|image|max:10240',
            'hex_code'   => 'nullable|string|max:20',
        ];
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
        $this->hex_code    = $color->hex_code ?? null;
        $this->description = $color->description ?? '';
        $this->status      = $color->status ?? 'Active';
        
        $user = auth()->user();
        $adminId = $user?->Admin_ID;
        if (!$adminId && $user) {
            $adminId = $user?->admin_id ?? $user?->Admin_ID ?? $user?->id ?? $user?->email;
        }

        $this->admin_id = \App\Models\Admin::formatId($adminId ?: ($color->admin_id ?: 'ADM-001'));
        $this->existingImage = $color->image ?? null;
        $this->image = null;

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;

        $this->reset(['colorId', 'hue', 'value', 'chroma', 'full_code', 'color_name', 'hex_code', 'description', 'status', 'admin_id', 'image', 'existingImage']);

        $this->resetErrorBag();
    }

    public function applyPickedColor(string $hex, int $r, int $g, int $b)
    {
        $this->hex_code = $hex;

        try {
            /** @var \App\Services\SoilScanService $scanner */
            $scanner = app(\App\Services\SoilScanService::class);
            $matched = $scanner->matchRgbToMunsell($r, $g, $b);
            if ($matched) {
                $this->hue = $matched['hue'];
                $this->value = $matched['value'];
                $this->chroma = $matched['chroma'];
                $this->full_code = $matched['full_code'];

                if (empty($this->color_name) || $this->color_name === 'Determined Soil Specimen') {
                    $this->color_name = $matched['color_name'];
                }
                if (empty($this->description) && !empty($matched['description'])) {
                    $this->description = $matched['description'];
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('applyPickedColor failed: ' . $e->getMessage());
        }
    }

    public function updatedHue()
    {
        $this->updateFullCode();
        $this->syncHexFromNotation();
    }

    public function updatedValue()
    {
        $this->updateFullCode();
        $this->syncHexFromNotation();
    }

    public function updatedChroma()
    {
        $this->updateFullCode();
        $this->syncHexFromNotation();
    }

    private function syncHexFromNotation()
    {
        if ($this->hue !== '' && $this->value !== '' && $this->chroma !== '') {
            $this->hex_code = ManageMunsellColor::munsellToHex($this->hue, $this->value, $this->chroma);
        }
    }

    private function updateFullCode()
    {
        if ($this->hue !== '' && $this->value !== '' && $this->chroma !== '') {
            $this->full_code = trim($this->hue . ' ' . $this->value . '/' . $this->chroma);
        } else {
            $this->full_code = '';
        }
    }

    public function save()
    {
        $this->validate();

        $color = MunsellColor::find($this->colorId);

        if (! $color) {
            return;
        }

        $user = auth()->user();
        $adminIdToSave = $user?->Admin_ID ?: $this->admin_id;
        if (empty($adminIdToSave)) {
            $firstAdmin = \App\Models\Admin::first();
            $adminIdToSave = $firstAdmin ? $firstAdmin->Admin_ID : 'ADM001';
        }

        if (empty($this->hex_code) && $this->hue !== '' && $this->value !== '' && $this->chroma !== '') {
            $this->hex_code = ManageMunsellColor::munsellToHex($this->hue, $this->value, $this->chroma);
        }

        $data = [
            'hue'        => $this->hue,
            'value'      => $this->value,
            'chroma'     => $this->chroma,
            'full_code'  => $this->full_code,
            'color_name' => $this->color_name,
            'hex_code'   => $this->hex_code,
            'description'=> $this->description,
            'status'     => $this->status,
            'admin_id'      => $color->admin_id ?: $adminIdToSave,
            'last_admin_id' => $adminIdToSave,
            'updated_at'    => now(),
        ];

        if ($this->image) {
            $path = $this->image->store('munsell_colors', 'public');
            $data['image'] = $path;
            $data['image_path'] = $path;
        }

        $color->update($data);

        // Synchronize related Soil records to maintain single-source-of-truth consistency
        \App\Models\Soil::where('color_id', $color->munsell_color_id)
            ->orWhere('full_code', $color->full_code)
            ->update([
                'color_id'    => $color->munsell_color_id,
                'soil_name'   => $this->color_name,
                'description' => $this->description,
                'hue'         => $this->hue,
            ]);

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
        $admins = \App\Models\Admin::all();

        return view('admin.livewire.munsell-color.munsell-edit-modal', [
            'availableHues' => $hues,
            'admins'        => $admins,
        ]);
    }
}
