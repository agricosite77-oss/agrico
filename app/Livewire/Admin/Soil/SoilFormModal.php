<?php

namespace App\Livewire\Admin\Soil;

use App\Models\MunsellColor;
use App\Models\Soil;
use App\Models\SoilScan;
use Livewire\Component;
use Livewire\WithFileUploads;

class SoilFormModal extends Component
{
    use WithFileUploads;

    public $showModal = false;

    public $soilId = null;
    public $scanId = null;

    public $name = '';
    public $description = '';
    public $scan_note = '';
    public $soil_type = '';
    public $organic_matter = '';

    public $hue = '';
    public $value = '';
    public $chroma = '';
    public $full_code = '';

    public $status = 'Active';
    public $admin_id = '';
    public $user_id = '';

    public $image = null;
    public $existingImage = null;

    protected $listeners = [
        'edit-soil' => 'openModal',
    ];

    protected function rules()
    {
        return [
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string|max:65000',
            'scan_note'      => 'nullable|string|max:65000',
            'soil_type'      => 'nullable|string|max:100',
            'organic_matter' => 'nullable|string|max:100',
            'hue'            => 'nullable|string|max:20',
            'value'          => 'nullable|numeric|min:0|max:10',
            'chroma'         => 'nullable|numeric|min:0|max:20',
            'full_code'      => 'nullable|string|max:30',
            'status'         => 'nullable|in:Active,Inactive',
            'admin_id'       => 'nullable|string|max:50',
            'user_id'        => 'nullable|string|max:50',
            'image'          => 'nullable|image|max:10240',
        ];
    }

    public function openModal($id)
    {
        $scan = SoilScan::with('soil.munsellColor')->find($id);
        $soil = ($scan && $scan->soil) ? $scan->soil : Soil::with('munsellColor')->find($id);

        if (!$soil) {
            return;
        }

        $this->scanId        = $scan ? $scan->soil_scan_id : null;
        $this->soilId        = $soil->soil_id;
        $this->name          = $soil->effective_soil_name;
        $this->description   = $soil->effective_description;
        $this->scan_note     = $scan ? ($scan->has_user_scan_note ? $scan->scan_note : '') : '';
        $this->soil_type     = $soil->soil_type ?? $soil->computed_soil_type;
        $this->organic_matter= $soil->organic_matter ?? $soil->computed_organic_matter;
        $this->status        = $soil->status ?? 'Active';

        $munsell             = $soil->munsellColor ?? $soil->dynamic_munsell_color;
        $this->hue           = $soil->hue ?? ($munsell->hue ?? '10R');
        $this->value         = $munsell->value ?? $soil->dynamic_value;
        $this->chroma        = $munsell->chroma ?? $soil->dynamic_chroma;
        $this->full_code     = $soil->full_code ?? ($munsell->full_code ?? '');

        $loggedInUser = auth('admin')->user() ?? auth('web')->user() ?? auth()->user();
        $userAdminId = $loggedInUser?->admin_id ?? $loggedInUser?->Admin_ID ?? $loggedInUser?->id ?? $loggedInUser?->email;
        $adminCode = \App\Models\Admin::formatId($userAdminId ?: ($soil->admin_id ?: 'ADM-001'));
        if (!$adminCode || $adminCode === '—' || str_contains($adminCode, '-000')) {
            $adminCode = \App\Models\Admin::formatId($userAdminId ?: 'ADM-001');
        }
        $this->admin_id = $adminCode;
        $this->user_id  = $loggedInUser?->user_id ?? $loggedInUser?->User_ID ?? $scan?->user_id ?? $soil->user_id ?? 'ADM-001';
        $this->existingImage = $soil->image ?? null;
        $this->image         = null;

        $this->showModal     = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset([
            'soilId', 'scanId', 'name', 'description', 'scan_note', 'soil_type', 'organic_matter',
            'hue', 'value', 'chroma', 'full_code', 'status', 'admin_id', 'user_id', 'image', 'existingImage'
        ]);
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
        if ($this->hue !== '' && $this->value !== '' && $this->chroma !== '') {
            $this->full_code = trim($this->hue . ' ' . $this->value . '/' . $this->chroma);
        }
    }

    public function save()
    {
        $this->validate();

        $soil = Soil::find($this->soilId);
        if (!$soil) {
            return;
        }

        $user = auth('admin')->user() ?? auth('web')->user() ?? auth()->user();
        $userAdminId = $user?->admin_id ?? $user?->Admin_ID ?? $user?->id ?? $user?->email;
        $adminIdToSave = \App\Models\Admin::formatId($this->admin_id ?: ($userAdminId ?: 'ADM-001'));

        $munsellColor = null;
        if ($this->full_code) {
            $munsellColor = MunsellColor::where('full_code', $this->full_code)->first();
        }

        $data = [
            'soil_name'      => $this->name,
            'description'    => $this->description,
            'soil_type'      => $this->soil_type,
            'organic_matter' => $this->organic_matter,
            'hue'            => $this->hue ?: ($soil->hue ?? '10R'),
            'full_code'      => $this->full_code ?: $soil->full_code,
            'status'         => $this->status,
            'admin_id'       => $adminIdToSave,
            'last_admin_id'  => $adminIdToSave,
            'color_id'       => $munsellColor ? $munsellColor->munsell_color_id : $soil->color_id,
            'updated_at'     => now(),
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('soils', 'public');
        }

        if ($this->scanId) {
            $scan = SoilScan::find($this->scanId);
            if ($scan) {
                // Check if the current Soil record is shared by other scan records
                $otherScansCount = SoilScan::where('soil_id', $soil->soil_id)
                    ->where('soil_scan_id', '!=', $scan->soil_scan_id)
                    ->count();

                if ($otherScansCount > 0) {
                    // Replicate into a dedicated Soil record for this specific scan
                    $data['scan_count'] = 1;
                    $newSoil = Soil::create($data);

                    if ($soil->scan_count > 1) {
                        $soil->decrement('scan_count');
                    }

                    $scan->update([
                        'soil_id'    => $newSoil->soil_id,
                        'scan_note'  => !empty(trim($this->scan_note ?? '')) ? trim($this->scan_note) : null,
                        'updated_at' => now(),
                    ]);
                } else {
                    // This scan is the only record attached to this Soil, update directly
                    $soil->update($data);
                    $scan->update([
                        'scan_note'  => !empty(trim($this->scan_note ?? '')) ? trim($this->scan_note) : null,
                        'updated_at' => now(),
                    ]);
                }
            }
        } else {
            $soil->update($data);
        }

        $this->dispatch('soil-updated');
        $this->closeModal();
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

        return view('admin.livewire.soil.soil-form-modal', [
            'availableHues' => $hues,
        ]);
    }
}
