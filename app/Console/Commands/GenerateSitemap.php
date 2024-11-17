<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap.';

    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = SitemapGenerator::create(config('app.url'))
            ->hasCrawled(function (Url $url) {
                // Excluir rutas de administración
                if (str_contains($url->url, '/admin') ||
                    str_contains($url->url, '/filament') ||
                    str_contains($url->url, '/livewire')) {
                    return;
                }

                // Configurar prioridades según la ruta
                $priority = 0.8;
                $changeFreq = 'weekly';

                if ($url->url === config('app.url')) {
                    $priority = 1.0;
                    $changeFreq = 'daily';
                } elseif (str_contains($url->url, 'about')) {
                    $priority = 0.9;
                    $changeFreq = 'monthly';
                }

                return $url
                    ->setPriority($priority)
                    ->setChangeFrequency($changeFreq);
            })
            ->getSitemap();

        // Agregar URLs manualmente
        $pages = ['about', 'service', 'contact', 'faq', 'policy'];

        foreach ($pages as $page) {
            $sitemap->add(
                Url::create("/{$page}")
                    ->setPriority(0.8)
                    ->setChangeFrequency('weekly')
            );
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully!');
    }
}
