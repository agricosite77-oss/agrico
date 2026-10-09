<?php

namespace App\View\Components\Dashboard;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HueMonitor extends Component
{
    public $munsellHues;

    public function __construct($munsellHues = [])
    {
        $this->munsellHues = $munsellHues;
    }

    public function render(): View|Closure|string
    {
        return view('components.dashboard.hue-monitor', [

            'munsellHues' => $this->munsellHues,

        ]);
    }
}
