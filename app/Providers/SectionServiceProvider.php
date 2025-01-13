<?php

namespace App\Providers;

use App\Services\SectionRenderer;
use App\View\Components\RenderSection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class SectionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SectionRenderer::class, function ($app) {
            return new SectionRenderer();
        });
    }

    public function boot(): void
    {
        // Registrar el componente Blade
        Blade::component('section', RenderSection::class);

        // Registrar directiva Blade para renderizar secciones
        Blade::directive('rendersections', function ($expression) {
            return "<?php echo app(\App\Services\SectionRenderer::class)->renderSections($expression); ?>";
        });
    }
}
