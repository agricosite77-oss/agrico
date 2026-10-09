<?php

namespace App\Livewire\Admin\MunsellColor;

use App\Models\MunsellColor;
use Livewire\Component;
use Livewire\WithFileUploads;

class MunsellCreateModal extends Component
{
    use WithFileUploads;

    public $showModal = false;
    public $hue = '';
    public $value = '';
    public $chroma = '';
    public $full_code = '';
    public $color_name = '';
    public $description = '';
    public $status = 'Active';
    public $admin_id = '';
    public $image = null;
    public $hex_code = null;

    protected $listeners = [
        'create-munsell' => 'openModal',
        'apply-picked-color' => 'applyPickedColor',
    ];

    protected $rules = [
        'image'      => 'nullable|image|max:10240',
        'color_name' => 'required|string|max:100',
        'description'=> 'required|string|max:65000',
        'hue'        => 'required|string|max:20',
        'value'      => 'required|numeric|min:0|max:10',
        'chroma'     => 'required|numeric|min:0|max:20',
        'full_code'  => 'required|string|max:30',
        'status'     => 'required|in:Active,Inactive',
        'admin_id'   => 'nullable|string|max:50',
        'hex_code'   => 'nullable|string|max:20',
    ];

    public function openModal()
    {
        $this->reset(['hue', 'value', 'chroma', 'full_code', 'color_name', 'hex_code', 'description', 'status', 'admin_id', 'image']);
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
        $this->reset(['hue', 'value', 'chroma', 'full_code', 'color_name', 'hex_code', 'description', 'status', 'admin_id', 'image']);
        $this->resetErrorBag();
    }

    public function applyPickedColor($hex = '', ?string $notation = null)
    {
        if (is_array($hex)) {
            $notation = $hex['notation'] ?? $notation;
            $hex = (string)($hex['hex'] ?? '');
        }

        $this->hex_code = $hex;

        if ($notation) {
            $trimmed = trim($notation);
            if (preg_match('/^([\d.]+[A-Z]+)\s+([\d.]+)\/([\d.]+)$/', $trimmed, $m)) {
                $this->hue = $m[1];
                $this->value = $m[2];
                $this->chroma = $m[3];
                $this->updateFullCode();
            } elseif (preg_match('/^N\s*([\d.]+)(?:\/([\d.]*))?$/i', $trimmed, $m)) {
                $this->hue = 'N';
                $this->value = $m[1];
                $this->chroma = !empty($m[2]) ? $m[2] : '0';
                $this->updateFullCode();
            }
        }

        // SoilScanService enrichment (color name, description, and munsell matching)
        try {
            /** @var \App\Services\SoilScanService $scanner */
            $scanner = app(\App\Services\SoilScanService::class);
            $cleanHex = ltrim((string)$hex, '#');
            if (strlen($cleanHex) === 6) {
                $r = hexdec(substr($cleanHex, 0, 2));
                $g = hexdec(substr($cleanHex, 2, 2));
                $b = hexdec(substr($cleanHex, 4, 2));
                $matched = $scanner->analyzeImageFromRgb(['r' => $r, 'g' => $g, 'b' => $b]);
                if ($matched && !empty($matched['success'])) {
                    if ($this->hue === '' || $this->value === '' || $this->chroma === '') {
                        $this->hue = $matched['hue'] ?? $this->hue;
                        $this->value = $matched['value'] ?? $this->value;
                        $this->chroma = $matched['chroma'] ?? $this->chroma;
                        $this->updateFullCode();
                    }
                    if (empty($this->color_name) || $this->color_name === 'Determined Soil Specimen') {
                        $this->color_name = $matched['color_name'] ?? $this->color_name;
                    }
                    if (empty($this->description) && !empty($matched['description'])) {
                        $this->description = $matched['description'];
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('SoilScanService enrichment failed in applyPickedColor: ' . $e->getMessage());
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
            'admin_id'   => $adminIdToSave,
        ];

        if ($this->image) {
            $path = $this->image->store('munsell_colors', 'public');
            $data['image'] = $path;
            $data['image_path'] = $path;
        }

        MunsellColor::create($data);

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
        if (!empty($this->hue) && !in_array($this->hue, $hues)) {
            $hues[] = $this->hue;
        }
        $admins = \App\Models\Admin::all();

        return view('admin.livewire.munsell-color.munsell-create-modal', [
            'availableHues' => $hues,
            'admins'        => $admins,
        ]);
    }
}
