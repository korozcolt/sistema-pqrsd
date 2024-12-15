<?php

namespace App\Console\Commands\Cms;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\{Page, Section, Widget, Menu, MenuItem, ContentType};
use Illuminate\Support\Facades\DB;

class MakeComponent extends Command
{
    protected $signature = 'cms:make
                            {type : Type of component (section/widget)}
                            {name : Name of the component}
                            {--position=content : Position in layout}
                            {--description= : Component description}
                            {--editable : Whether the component is editable}';

    protected $description = 'Create a new CMS component';

    public function handle()
    {
        $type = $this->argument('type');
        $name = $this->argument('name');
        $position = $this->option('position');
        $description = $this->option('description');
        $editable = $this->option('editable');

        // Crear componente Livewire
        $componentName = Str::studly($name);
        $namespace = $type === 'section' ? 'Sections' : 'Widgets';

        $this->call('make:livewire', [
            'name' => "Cms/{$namespace}/{$componentName}",
        ]);

        // Registrar en base de datos
        $model = $type === 'section' ? Section::class : Widget::class;

        $model::create([
            'name' => $name,
            'identifier' => Str::slug($name),
            'component_name' => "cms.{$namespace}.{$componentName}",
            'layout_position' => $position,
            'description' => $description,
            'is_editable' => $editable,
            'is_active' => true
        ]);

        $this->info("CMS {$type} '{$name}' created successfully!");
    }
}

class CreatePage extends Command
{
    protected $signature = 'cms:create-page
                            {name : Name of the page}
                            {--layout=default : Layout template}
                            {--slug= : Custom URL slug}
                            {--title= : Meta title}
                            {--description= : Meta description}
                            {--status=draft : Page status}';

    protected $description = 'Create a new CMS page';

    public function handle()
    {
        $name = $this->argument('name');
        $slug = $this->option('slug') ?? Str::slug($name);

        $page = Page::create([
            'name' => $name,
            'slug' => $slug,
            'layout' => $this->option('layout'),
            'meta_title' => $this->option('title') ?? $name,
            'meta_description' => $this->option('description'),
            'status' => $this->option('status'),
            'is_active' => true
        ]);

        $this->info("Page '{$name}' created successfully!");
    }
}

class AssignComponent extends Command
{
    protected $signature = 'cms:assign
                            {page : Page slug or ID}
                            {component : Component identifier}
                            {--position=content : Layout position}
                            {--order=0 : Display order}
                            {--config= : JSON configuration}';

    protected $description = 'Assign a component to a page';

    public function handle()
    {
        $page = is_numeric($this->argument('page'))
            ? Page::findOrFail($this->argument('page'))
            : Page::where('slug', $this->argument('page'))->firstOrFail();

        $component = Section::where('identifier', $this->argument('component'))
            ->orWhere(function ($query) {
                $query->where('identifier', $this->argument('component'));
            })->firstOrFail();

        $page->sections()->attach($component->id, [
            'position' => $this->option('position'),
            'order' => $this->option('order'),
            'config' => $this->option('config') ? json_decode($this->option('config'), true) : null
        ]);

        $this->info("Component assigned to page successfully!");
    }
}

class PublishPage extends Command
{
    protected $signature = 'cms:publish {page : Page slug or ID}';

    protected $description = 'Publish a CMS page';

    public function handle()
    {
        $page = is_numeric($this->argument('page'))
            ? Page::findOrFail($this->argument('page'))
            : Page::where('slug', $this->argument('page'))->firstOrFail();

        $page->update([
            'status' => 'published',
            'published_at' => now()
        ]);

        $this->info("Page '{$page->name}' published successfully!");
    }
}

class UnpublishPage extends Command
{
    protected $signature = 'cms:unpublish {page : Page slug or ID}';

    protected $description = 'Unpublish a CMS page';

    public function handle()
    {
        $page = is_numeric($this->argument('page'))
            ? Page::findOrFail($this->argument('page'))
            : Page::where('slug', $this->argument('page'))->firstOrFail();

        $page->update([
            'status' => 'draft',
            'published_at' => null
        ]);

        $this->info("Page '{$page->name}' unpublished successfully!");
    }
}

class ListComponents extends Command
{
    protected $signature = 'cms:list {type? : Type of components to list}';

    protected $description = 'List all CMS components';

    public function handle()
    {
        $type = $this->argument('type');

        if ($type) {
            $model = match ($type) {
                'sections' => Section::class,
                'widgets' => Widget::class,
                'pages' => Page::class,
                default => null
            };

            if (!$model) {
                $this->error("Invalid component type: {$type}");
                return;
            }

            $components = $model::all();
        } else {
            $sections = Section::all();
            // Aquí está la corrección
            $widgets = Widget::all(); // Obtenemos la colección de widgets
            $pages = Page::all();

            $this->info("=== Sections ===");
            $this->table(
                ['ID', 'Name', 'Identifier', 'Position', 'Status'],
                $sections->map->only(['id', 'name', 'identifier', 'layout_position', 'status'])
            );

            $this->info("\n=== Widgets ===");
            $this->table(
                ['ID', 'Name', 'Identifier', 'Type', 'Status'],
                $widgets->map->only(['id', 'name', 'identifier', 'type', 'status'])
            );

            $this->info("\n=== Pages ===");
            $this->table(
                ['ID', 'Name', 'Slug', 'Status', 'Published'],
                $pages->map->only(['id', 'name', 'slug', 'status', 'published_at'])
            );
        }
    }
}

class RemoveComponent extends Command
{
    protected $signature = 'cms:remove {page} {component}';

    protected $description = 'Remove a component from a page';

    public function handle()
    {
        $page = is_numeric($this->argument('page'))
            ? Page::findOrFail($this->argument('page'))
            : Page::where('slug', $this->argument('page'))->firstOrFail();

        $component = Section::where('identifier', $this->argument('component'))
            ->orWhere(function ($query) {
                $query->where('identifier', $this->argument('component'));
            })->firstOrFail();

        $page->sections()->detach($component->id);

        $this->info("Component removed from page successfully!");
    }
}

class ReorderComponents extends Command
{
    protected $signature = 'cms:reorder {page} {component} {--order=}';

    protected $description = 'Reorder components on a page';

    public function handle()
    {
        $page = is_numeric($this->argument('page'))
            ? Page::findOrFail($this->argument('page'))
            : Page::where('slug', $this->argument('page'))->firstOrFail();

        $component = Section::where('identifier', $this->argument('component'))->firstOrFail();

        $page->sections()->updateExistingPivot($component->id, [
            'order' => $this->option('order')
        ]);

        $this->info("Component reordered successfully!");
    }
}

class ExportPage extends Command
{
    protected $signature = 'cms:export {page}';

    protected $description = 'Export a page configuration';

    public function handle()
    {
        $page = is_numeric($this->argument('page'))
            ? Page::findOrFail($this->argument('page'))
            : Page::where('slug', $this->argument('page'))->firstOrFail();

        $config = [
            'page' => $page->toArray(),
            'sections' => $page->sections()->with('content')->get()->toArray()
        ];

        $filename = storage_path("app/exports/page-{$page->slug}.json");
        file_put_contents($filename, json_encode($config, JSON_PRETTY_PRINT));

        $this->info("Page exported to: {$filename}");
    }
}

class ImportPage extends Command
{
    protected $signature = 'cms:import {file}';

    protected $description = 'Import a page configuration';

    public function handle()
    {
        $file = $this->argument('file');
        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return;
        }

        $config = json_decode(file_get_contents($file), true);

        DB::transaction(function () use ($config) {
            $page = Page::create($config['page']);

            foreach ($config['sections'] as $section) {
                $sectionModel = Section::find($section['id']) ?? Section::create($section);
                $page->sections()->attach($sectionModel->id, [
                    'order' => $section['pivot']['order'] ?? 0
                ]);
            }
        });

        $this->info("Page imported successfully!");
    }
}
