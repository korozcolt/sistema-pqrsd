<?php

namespace App\Livewire\Sections;

use Livewire\Component;
use App\Models\Section;

class Testimonials extends Component
{
    public Section $section;
    public array $content;
    public array $settings;

    public function mount(Section $section)
    {
        $this->section = $section;
        $this->content = $section->content ?? [];
        $this->settings = $section->settings ?? [
            'style' => 'cards', // cards, simple, minimal
            'show_images' => true,
            'autoplay' => true,
            'interval' => 5000,
            'background' => 'white'
        ];
    }

    protected function getTestimonialClasses(): string
    {
        return match($this->settings['style']) {
            'cards' => 'bg-white rounded-lg shadow-lg p-6',
            'simple' => 'bg-white p-6',
            'minimal' => 'p-6',
            default => 'bg-white rounded-lg shadow-lg p-6'
        };
    }

    public function render()
    {
        return view('livewire.sections.testimonials', [
            'title' => $this->content['title'] ?? '',
            'description' => $this->content['description'] ?? '',
            'testimonials' => $this->content['testimonials'] ?? [],
            'testimonialClasses' => $this->getTestimonialClasses()
        ]);
    }
}
