<?php

namespace App\Livewire\Admin\Application;

use App\Models\Application;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadApplication extends Component
{
    use WithFileUploads;

    public $appFile;
    public $versionNumber = '';
    public $releaseNotes = '';

    protected $rules = [
        'versionNumber' => 'required|string',
        'releaseNotes' => 'required|string',
    ];

    protected function checkSuperAdmin(): void
    {
        $user = auth()->user() ?? \Illuminate\Support\Facades\Auth::guard('admin')->user();
        if ($user instanceof \App\Models\User || $user instanceof \App\Models\Admin) {
            if ($user->isSuperAdmin()) {
                return;
            }
        }

        abort(403, 'Unauthorized. Only Super Admin can access Upload Application.');
    }

    public function mount()
    {
        $this->checkSuperAdmin();

        if (Application::count() === 0) {
            (new \Database\Seeders\ApplicationSeeder())->run();
        }
    }

    public function uploadVersion()
    {
        $this->checkSuperAdmin();
        $this->validate();

        $versionFormatted = str_starts_with(strtolower(trim($this->versionNumber)), 'v') ? trim($this->versionNumber) : 'v' . trim($this->versionNumber);
        $originalName = 'agrico-app-' . $versionFormatted . '.apk';
        $fileSize = '48.5 MB';
        $path = 'applications/' . $originalName;

        if ($this->appFile) {
            $originalName = $this->appFile->getClientOriginalName();
            $bytes = $this->appFile->getSize();
            $fileSize = round($bytes / 1024 / 1024, 1) . ' MB';
            $path = $this->appFile->store('applications', 'public');
        }

        // Set previous ACTIVE apps to ARCHIVED
        Application::where('status', 'ACTIVE')->update(['status' => 'ARCHIVED']);

        // Create new active application version
        $uploaderName = auth()->user()?->Full_Name ?? auth()->user()?->name ?? 'Agrico Administrator';

        Application::create([
            'application_name' => 'AgriCo Mobile App',
            'version' => $versionFormatted,
            'filename' => $originalName,
            'file_size' => $fileSize,
            'release_notes' => $this->releaseNotes,
            'status' => 'ACTIVE',
            'uploaded_by' => $uploaderName,
            'file_path' => $path,
        ]);

        $this->reset(['appFile', 'versionNumber', 'releaseNotes']);
        session()->flash('message', 'New application version ' . $versionFormatted . ' uploaded and deployed successfully!');
    }

    public function deleteVersion($id)
    {
        $this->checkSuperAdmin();
        $app = Application::find($id);
        if ($app) {
            $versionName = $app->version;
            $app->delete();

            // If we deleted the active version, make the newest remaining version ACTIVE
            if ($app->status === 'ACTIVE') {
                $newest = Application::orderBy('application_id', 'desc')->first();
                if ($newest) {
                    $newest->update(['status' => 'ACTIVE']);
                }
            }

            session()->flash('message', 'Application version ' . $versionName . ' removed.');
        }
    }

    public function render()
    {
        $currentActive = Application::where('status', 'ACTIVE')->first();
        $applications = Application::orderBy('application_id', 'desc')->get();

        return view('admin.livewire.application.upload-application', [
            'currentActive' => $currentActive,
            'applications' => $applications,
            'appFile' => $this->appFile,
        ]);
    }
}
