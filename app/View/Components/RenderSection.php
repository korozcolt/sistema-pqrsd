<?php

namespace App\View\Components;

use App\Models\Section;
use App\Services\SectionRenderer;
use Illuminate\View\Component;

class RenderSection extends Component
{
    public function __construct(
        public Section $section,
        protected SectionRenderer $renderer
    ) {}

    public function render()
    {
        return $this->renderer->render($this->section);
    }
}
