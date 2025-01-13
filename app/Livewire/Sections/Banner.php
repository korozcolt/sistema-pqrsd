<?php

namespace App\Livewire\Sections;

use Livewire\Component;
use App\Models\Section;

class Banner extends Component
{
    public Section $section;
    public array $content;
    public array $settings;

    public function mount(Section $section)
    {
        $this->section = $section;
        $this->content = $section->content ?? [];
        $this->settings = $section->settings ?? [
            'height' => 'medium', // small, medium, large
            'text_alignment' => 'center',
            'overlay_opacity' => 60,
            'text_color' => 'white'
        ];
    }

    protected function getHeightClass(): string
    {
        return match($this->settings['height']) {
            'small' => 'min-h-[300px]',
            'large' => 'min-h-[600px]',
            default => 'min-h-[400px]'
        };
    }

    public function render()
    {
        return view('livewire.sections.banner', [
            'title' => $this->content['title'] ?? '',
            'description' => $this->content['description'] ?? '',
            'background' => $this->content['background'] ?? '',
            'cta_text' => $this->content['cta_text'] ?? '',
            'cta_url' => $this->content['cta_url'] ?? '',
            'heightClass' => $this->getHeightClass()
        ]);
    }
}
