<?php

namespace App\Livewire\Sections;

use Livewire\Component;
use App\Models\Section;

class Services extends Component
{
    public Section $section;
    public array $content;
    public array $settings;

    public function mount(Section $section)
    {
        $this->section = $section;
        $this->content = $section->content ?? [];
        $this->settings = $section->settings ?? [
            'columns' => 3,
            'show_icons' => true,
            'card_style' => 'shadow',
            'background' => 'white'
        ];
    }

    protected function getCardClasses(): string
    {
        return match($this->settings['card_style']) {
            'shadow' => 'shadow-lg hover:shadow-xl',
            'border' => 'border border-gray-200',
            default => ''
        } . ' bg-white rounded-lg p-6 transition duration-300';
    }

    public function render()
    {
        return view('livewire.sections.services', [
            'title' => $this->content['title'] ?? '',
            'description' => $this->content['description'] ?? '',
            'services' => $this->content['services'] ?? [],
            'cardClasses' => $this->getCardClasses(),
            'gridColumns' => $this->settings['columns']
        ]);
    }
}
