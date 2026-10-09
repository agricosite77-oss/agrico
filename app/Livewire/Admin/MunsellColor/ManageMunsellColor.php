<?php

namespace App\Livewire\Admin\MunsellColor;

use App\Models\MunsellColor;
use Livewire\Component;
use Livewire\WithPagination;

class ManageMunsellColor extends Component
{
    use WithPagination;

    public $search = '';
    public $organicIndex = '';
    public $soilType = '';
    public $hue = '';
    public $selectedDate = '';

    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $listeners = [
        'munsell-saved' => '$refresh',
        'munsell-deleted' => '$refresh',
    ];

    public function toggleStatus($id)
    {
        $color = MunsellColor::find($id);
        if ($color) {
            $user = auth()->user();
            $adminId = $user?->Admin_ID ?? $user?->admin_id;

            if (!$adminId && $user) {
                $userEmail = $user->Email ?? $user->email;
                if ($userEmail) {
                    $adminRec = \App\Models\Admin::where('email', $userEmail)->first();
                    $adminId = $adminRec?->admin_id ?? $adminRec?->Admin_ID;
                }
            }

            if (!$adminId) {
                $firstAdmin = \App\Models\Admin::first();
                $adminId = $firstAdmin?->admin_id ?? 'ADM-001';
            }

            $color->status = ($color->status === 'Inactive') ? 'Active' : 'Inactive';
            $color->last_admin_id = \App\Models\Admin::formatId($adminId);
            $color->updated_at = now();
            $color->save();
        }
    }

    public function sortBy($field)
    {
        $allowedFields = [
            'id',
            'full_code',
            'color_name',
            'soil_organic_index',
            'soil_type',
            'hue',
            'value',
            'chroma',
            'admin_id',
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

    public function selectSuggestion($value)
    {
        $this->search = $value;
        $this->organicIndex = '';
        $this->soilType = '';
        $this->hue = '';
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedOrganicIndex()
    {
        $this->resetPage();
    }

    public function updatedSoilType()
    {
        $this->resetPage();
    }

    public function updatedHue()
    {
        $this->resetPage();
    }

    public function updatedSelectedDate()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $queryBuilder = function () {
            return MunsellColor::query()
                ->when($this->selectedDate, function ($query) {
                    $query->whereDate('created_at', $this->selectedDate);
                })
                ->when($this->search, function ($query) {
                    $searchStr = trim($this->search);
                    $query->where(function ($q) use ($searchStr) {
                        if (str_starts_with($searchStr, '#')) {
                            $idNum = ltrim($searchStr, '#');
                            $q->where('munsell_color_id', $idNum);
                        } else {
                            $q->where('full_code', 'like', '%'.$searchStr.'%')
                                ->orWhere('color_name', 'like', '%'.$searchStr.'%')
                                ->orWhere('soil_organic_index', 'like', '%'.$searchStr.'%')
                                ->orWhere('soil_type', 'like', '%'.$searchStr.'%')
                                ->orWhere('description', 'like', '%'.$searchStr.'%')
                                ->orWhere('hue', 'like', '%'.$searchStr.'%')
                                ->orWhere('admin_id', 'like', '%'.$searchStr.'%');
                        }
                    });
                })
                ->when($this->organicIndex, function ($query) {
                    $query->where('soil_organic_index', $this->organicIndex);
                })
                ->when($this->soilType, function ($query) {
                    $query->where('soil_type', $this->soilType);
                })
                ->when($this->hue, function ($query) {
                    $query->where('hue', $this->hue);
                })
                ->orderBy($this->sortField === 'id' ? 'munsell_color_id' : $this->sortField, $this->sortDirection);
        };

        $colors = $queryBuilder()->paginate($this->perPage);

        if ($colors->currentPage() > $colors->lastPage() && $colors->lastPage() > 0) {
            $this->resetPage();
            $colors = $queryBuilder()->paginate($this->perPage);
        }

        $searchSuggestions = [];
        $trimmedSearch = trim($this->search);
        if (strlen($trimmedSearch) >= 1) {
            $searchSuggestions = MunsellColor::query()
                ->where(function ($q) use ($trimmedSearch) {
                    if (str_starts_with($trimmedSearch, '#')) {
                        $idNum = ltrim($trimmedSearch, '#');
                        $q->where('munsell_color_id', $idNum);
                    } else {
                        $q->where('full_code', 'like', '%'.$trimmedSearch.'%')
                          ->orWhere('color_name', 'like', '%'.$trimmedSearch.'%')
                          ->orWhere('soil_organic_index', 'like', '%'.$trimmedSearch.'%')
                          ->orWhere('soil_type', 'like', '%'.$trimmedSearch.'%')
                          ->orWhere('description', 'like', '%'.$trimmedSearch.'%')
                          ->orWhere('admin_id', 'like', '%'.$trimmedSearch.'%');
                    }
                })
                ->select('munsell_color_id as id', 'color_name', 'full_code', 'hue', 'value', 'chroma', 'hex_code', 'soil_organic_index', 'soil_type', 'admin_id')
                ->limit(8)
                ->get();
        }

        $organicIndexOptions = \App\Services\MunsellLookupService::getAllOrganicIndices();
        $soilTypeOptions = \App\Services\MunsellLookupService::getAllSoilTypes();

        $munsellHues = MunsellColor::query()
            ->whereNotNull('hue')
            ->distinct()
            ->orderBy('hue')
            ->pluck('hue')
            ->toArray();
        $defaultHues = ['10R', '2.5YR', '5YR', '7.5YR', '10YR', '2.5Y', '5Y'];
        $munsellHues = array_values(array_unique(array_merge($defaultHues, $munsellHues)));

        return view('admin.livewire.munsell-color.manage-munsell-color', [
            'colors' => $colors,
            'searchSuggestions' => $searchSuggestions,
            'organicIndexOptions' => $organicIndexOptions,
            'soilTypeOptions' => $soilTypeOptions,
            'munsellHues' => $munsellHues,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
    }

    public static function munsellToHex(string $hue, float|int|string $value, float|int|string $chroma, ?string $hexCode = null): string
    {
        if (!empty($hexCode) && (str_starts_with($hexCode, '#') || strlen(trim($hexCode)) >= 6)) {
            return str_starts_with($hexCode, '#') ? strtolower($hexCode) : '#' . strtolower($hexCode);
        }

        // 1. Direct hex lookup via comprehensive Munsell ASTM D1535 dataset
        $renotationHex = \App\Services\MunsellRenotationService::notationToHex($hue, $value, $chroma);
        if ($renotationHex) {
            return $renotationHex;
        }

        // 2. Calibrated multi-hue soil matrix
        $notationKey = strtoupper(trim($hue)) . ' ' . (int)$value . '/' . (int)$chroma;
        $calibratedMatrix = \App\Services\EncycolorpediaService::getCombinedMatrix();
        if (isset($calibratedMatrix[$notationKey])) {
            return strtolower($calibratedMatrix[$notationKey]['hex']);
        }

        // 3. Realistic soil-pigment HSL fallback
        $valueNum = (float) $value;
        $chromaNum = (float) $chroma;
        $hueMap = [
            'R' => 0,
            'YR' => 30,
            'Y' => 60,
            'GY' => 90,
            'G' => 120,
            'BG' => 165,
            'B' => 210,
            'PB' => 255,
            'P' => 285,
            'RP' => 330,
        ];

        preg_match('/^([\d.]+)?([a-zA-Z]+)$/', trim($hue), $matches);

        $numericPrefix = isset($matches[1]) && $matches[1] !== '' ? (float) $matches[1] : 5.0;
        $hueLetter = strtoupper($matches[2] ?? 'YR');

        $baseAngle = $hueMap[$hueLetter] ?? 30;
        $hueAngle = fmod($baseAngle + (($numericPrefix - 5.0) * 3.6) + 360.0, 360.0);

        // Munsell value 0-10 maps to lightness ~0-90%
        $lightness = ($valueNum / 10.0) * 85.0 + 5.0;
        // Soil pigments have moderate saturation; max chroma 16 ~ 65% sRGB saturation
        $saturation = min($chromaNum * 4.2, 60.0);

        // Convert HSL to Hex
        $sNorm = $saturation / 100.0;
        $lNorm = $lightness / 100.0;
        $c = (1.0 - abs(2.0 * $lNorm - 1.0)) * $sNorm;
        $x = $c * (1.0 - abs(fmod($hueAngle / 60.0, 2.0) - 1.0));
        $m = $lNorm - ($c / 2.0);

        if ($hueAngle < 60) {
            $rPrime = $c; $gPrime = $x; $bPrime = 0;
        } elseif ($hueAngle < 120) {
            $rPrime = $x; $gPrime = $c; $bPrime = 0;
        } elseif ($hueAngle < 180) {
            $rPrime = 0; $gPrime = $c; $bPrime = $x;
        } elseif ($hueAngle < 240) {
            $rPrime = 0; $gPrime = $x; $bPrime = $c;
        } elseif ($hueAngle < 300) {
            $rPrime = $x; $gPrime = 0; $bPrime = $c;
        } else {
            $rPrime = $c; $gPrime = 0; $bPrime = $x;
        }

        $r = (int) round(($rPrime + $m) * 255.0);
        $g = (int) round(($gPrime + $m) * 255.0);
        $b = (int) round(($bPrime + $m) * 255.0);

        return sprintf('#%02x%02x%02x', max(0, min(255, $r)), max(0, min(255, $g)), max(0, min(255, $b)));
    }
}
