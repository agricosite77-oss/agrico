<?php

namespace App\Livewire\Admin\Soil;

use App\Models\Soil;
use Livewire\Component;

class SoilDeleteModal extends Component
{
    public $showModal = false;
    public $soilId = null;
    public $scanId = null;

    protected $listeners = [
        'delete-soil' => 'openModal',
    ];

    public function openModal($id)
    {
        $scan = \App\Models\SoilScan::find($id);
        if ($scan) {
            $this->scanId = $scan->soil_scan_id;
            $this->soilId = $scan->soil_id;
        } else {
            $this->soilId = $id;
            $this->scanId = null;
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;

        $this->soilId = null;
        $this->scanId = null;
    }

    public function delete()
    {
        if ($this->scanId) {
            $scan = \App\Models\SoilScan::find($this->scanId);
            if ($scan) {
                $scan->delete();
            }
        } elseif ($this->soilId) {
            $soil = Soil::find($this->soilId);
            if ($soil) {
                $soil->delete();
            }
        }

        $this->dispatch('soil-deleted');
        $this->closeModal();
    }

    public function render()
    {
        return view('admin.livewire.soil.soil-delete-modal');
    }
}
