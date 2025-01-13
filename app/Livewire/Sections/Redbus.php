<?php

namespace App\Livewire\Sections;

use Livewire\Component;
use App\Models\Section;

class Redbus extends Component
{
    public Section $section;
    public array $content;
    public array $settings;

    public function mount(Section $section)
    {
        $this->section = $section;
        $this->content = $section->content ?? [];
        $this->settings = $section->settings ?? [
            'container_class' => 'shadow-lg',
            'padding' => 'lg'
        ];
    }

    public function render()
    {
        return view('livewire.sections.redbus', [
            'widgetId' => $this->content['widget_id'] ?? '',
            'position' => $this->content['position'] ?? 'center',
            'containerClass' => $this->settings['container_class'],
            'padding' => $this->settings['padding']
        ]);
    }
}
