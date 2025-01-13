<?php

namespace App\Livewire\Sections;

use Livewire\Component;
use App\Models\Section;

class Stats extends Component
{
    public Section $section;
    public array $content;
    public array $settings;

    public function mount(Section $section)
    {
        $this->section = $section;
        $this->content = $section->content ?? [];
        $this->settings = $section->settings ?? [
            'style' => 'cards', // cards, simple, bordered
            'columns' => 4,
            'animate' => true,
            'background' => 'white'
        ];
    }

    protected function getStatClasses(): string
    {
        return match($this->settings['style']) {
            'cards' => 'bg-white rounded-lg shadow-lg p-6',
            'simple' => 'p-6',
            'bordered' => 'border border-gray-200 rounded-lg p-6',
            default => 'p-6'
        };
    }

    public function render()
    {
        return view('livewire.sections.stats', [
            'title' => $this->content['title'] ?? '',
            'description' => $this->content['description'] ?? '',
            'stats' => $this->content['stats'] ?? [],
            'statClasses' => $this->getStatClasses()
        ]);
    }
}
