<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Kpi extends Component
{
    public $title;
    public $value;
    public $color;

    public function __construct($title, $value, $color = 'bg-maroon')
    {
        $this->title = $title;
        $this->value = $value;
        $this->color = $color;
    }

    public function render()
    {
        return view('components.kpi');
    }
}

