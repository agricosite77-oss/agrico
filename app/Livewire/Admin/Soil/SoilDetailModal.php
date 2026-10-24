<?php

namespace App\Livewire\Admin\Soil;

use App\Models\Soil;
use Livewire\Attributes\On;
use Livewire\Component;

class SoilDetailModal extends Component
{
    public $showModal = false;

    public $soil = null;
    public $scan = null;
    public $location = null;
    public $coverage = '';

    protected $listeners = [
        'view-soil' => 'openModal',
    ];

    #[On('view-soil')]
    public function openModal($id)
    {
        $scan = \App\Models\SoilScan::with(['soil.munsellColor', 'user', 'location'])->find($id);

        if ($scan && $scan->soil) {
            $this->scan = $scan;
            $this->soil = $scan->soil;
        } else {
            $this->soil = Soil::with(['munsellColor', 'user'])->find($id);
            $this->scan = $this->soil ? \App\Models\SoilScan::with(['soil.munsellColor', 'user', 'location'])->where('soil_id', $this->soil->soil_id)->latest('scan_date')->first() : null;
        }

        $loc = $this->scan?->location ?: \App\Models\LocationMapping::where('soil_scan_id', $this->scan?->soil_scan_id)->latest('location_mapping_id')->first();
        // Only fall back to soil_id lookup when the modal was opened directly from a Soil record
        // (not a SoilScan). When opened from a scan, show location ONLY if THAT scan was pinned.
        if (!$loc && $this->soil && !$this->scan) {
            $loc = \App\Models\LocationMapping::where('soil_id', $this->soil->soil_id)->latest('location_mapping_id')->first();
        }

        $this->location = $loc ? [
            'lat'        => (float) $loc->latitude,
            'lng'        => (float) $loc->longitude,
            'barangay'   => $loc->barangay ?: 'Lal-lo Jurisdiction',
            'status'     => $loc->status,
            'mapping_id' => $loc->location_mapping_id,
        ] : null;

        $covStr = '';
        if ($this->scan?->scan_note && preg_match('/\[Covered Area:\s*([^\]]+)\]/i', $this->scan->scan_note, $cm)) {
            $covStr = trim($cm[1]);
        }
        if (!$covStr && $loc) {
            $uReq = \App\Models\UserRequest::where('description', 'like', "%#{$loc->location_mapping_id}%")
                ->orWhere('description', 'like', "%({$loc->latitude}, {$loc->longitude})%")
                ->latest('user_request_id')->first();
            if ($uReq && preg_match('/\[Covered Area:\s*([^\]]+)\]/i', $uReq->description, $ucm)) {
                $covStr = trim($ucm[1]);
            }
        }
        $this->coverage = $covStr ?: '500 m²';

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->soil = null;
        $this->scan = null;
        $this->location = null;
        $this->coverage = '';
    }

    public function approveScan()
    {
        $id = $this->scan ? $this->scan->soil_scan_id : ($this->soil ? $this->soil->soil_id : null);
        if ($id) {
            \App\Models\SoilScan::approveScan($id);
            $this->dispatch('scan-moderated');
            $this->openModal($id);
            session()->flash('modal_message', 'Scan marked Verified and map pin published!');
        }
    }

    public function rejectScan()
    {
        $id = $this->scan ? $this->scan->soil_scan_id : ($this->soil ? $this->soil->soil_id : null);
        if ($id) {
            \App\Models\SoilScan::rejectScan($id);
            $this->dispatch('scan-moderated');
            $this->openModal($id);
            session()->flash('modal_message', 'Scan marked Rejected and map pin hidden.');
        }
    }

    public function edit()
    {
        $id = $this->scan ? $this->scan->soil_scan_id : ($this->soil ? $this->soil->soil_id : null);

        $this->closeModal();

        if ($id) {
            $this->dispatch('edit-soil', id: $id);
        }
    }

    public function render()
    {
        return view('admin.livewire.soil.soil-detail-modal');
    }
}
