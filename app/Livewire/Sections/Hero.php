<?php

namespace App\Livewire\Sections;

use Livewire\Component;
use App\Models\Section;

class Hero extends Component
{
    public Section $section;
    public array $content;
    public array $settings;

    public function mount(Section $section)
    {
        $this->section = $section;
        $this->content = $section->content ?? [];
        $this->settings = $section->settings ?? [
            'full_height' => true,
            'overlay_opacity' => 50,
            'text_color' => 'white'
        ];
    }

    public function render()
    {
        return view('livewire.sections.hero', [
            'title' => $this->content['title'] ?? '',
            'subtitle' => $this->content['subtitle'] ?? '',
            'background' => $this->content['background'] ?? '',
            'buttonText' => $this->content['button_text'] ?? '',
            'buttonUrl' => $this->content['button_url'] ?? '',
            'fullHeight' => $this->settings['full_height'],
            'overlayOpacity' => $this->settings['overlay_opacity'],
            'textColor' => $this->settings['text_color'],
        ]);
    }
}
