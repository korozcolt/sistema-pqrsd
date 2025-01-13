<?php

namespace App\Livewire\Sections;

use Livewire\Component;
use App\Models\Section;

class Text extends Component
{
    public Section $section;
    public array $content;
    public array $settings;

    public function mount(Section $section)
    {
        $this->section = $section;
        $this->content = $section->content ?? [];
        $this->settings = $section->settings ?? [
            'columns' => 1,
            'alignment' => 'left',
            'background' => 'white',
            'padding' => 'normal'
        ];
    }

    public function getColumnClass()
    {
        return match($this->settings['columns']) {
            2 => 'md:columns-2',
            3 => 'md:columns-3',
            default => ''
        };
    }

    public function getPaddingClass()
    {
        return match($this->settings['padding']) {
            'small' => 'py-8',
            'large' => 'py-24',
            default => 'py-16'
        };
    }

    public function render()
    {
        return view('livewire.sections.text', [
            'title' => $this->content['title'] ?? '',
            'content' => $this->content['content'] ?? '',
            'columnClass' => $this->getColumnClass(),
            'paddingClass' => $this->getPaddingClass(),
            'alignment' => $this->settings['alignment'],
            'background' => $this->settings['background']
        ]);
    }
}
