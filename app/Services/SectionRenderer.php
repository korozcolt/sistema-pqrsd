<?php

namespace App\Services;

use App\Models\Section;
use App\Enums\SectionType;
use Illuminate\Support\Facades\View;
use Illuminate\View\Component;

class SectionRenderer
{
    /**
     * Renderiza una sección basada en su tipo
     */
    public function render(Section $section): string
    {
        try {
            return View::make($this->getComponentName($section->type), [
                'section' => $section,
                'content' => $section->config, // Cambiado de content a config
                'settings' => array_merge(
                    $section->type->getDefaultSettings(),
                    $section->settings ?? []
                ),
            ])->render();
        } catch (\Throwable $e) {
            report($e);
            return '';
        }
    }

    /**
     * Obtiene el nombre del componente basado en el tipo
     */
    protected function getComponentName(SectionType $type): string
    {
        return 'livewire.sections.' . strtolower($type->value);
    }

    /**
     * Renderiza una colección de secciones
     */
    public function renderSections($sections): string
    {
        return $sections
            ->sortBy('order')
            ->map(fn($section) => $this->render($section))
            ->filter()
            ->join("\n");
    }
}
