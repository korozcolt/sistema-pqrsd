<?php

namespace App\Livewire\Sections;

use Livewire\Component;
use App\Models\Section;

class Features extends Component
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
            'style' => 'cards',
            'show_images' => true,
            'background' => 'white'
        ];
    }

    protected function getFeatureClasses(): string
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
        return view('livewire.sections.features', [
            'title' => $this->content['title'] ?? '',
            'subtitle' => $this->content['subtitle'] ?? '',
            'features' => $this->content['features'] ?? [],
            'featureClasses' => $this->getFeatureClasses()
        ]);
    }
}
