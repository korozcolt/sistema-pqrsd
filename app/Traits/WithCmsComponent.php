<?php

namespace App\Traits;

trait WithCmsComponent
{
    public $identifier;
    public $content = [];
    public $config = [];
    public $isEditing = false;
    public $isPreview = false;
    protected $componentType;

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'toggleEdit' => 'toggleEditing',
        'contentSaved' => 'onContentSaved'
    ];

    protected function initializeWithCmsComponent()
    {
        $this->componentType = static::getComponentType();
    }

    public function mount($identifier = null, array $content = [], array $config = [])
    {
        $this->identifier = $identifier;
        $this->content = array_merge($this->getDefaultContent(), $content);
        $this->config = array_merge($this->getDefaultConfig(), $config);

        if ($this->identifier) {
            $this->loadContent();
        }
    }

    protected function loadContent()
    {
        $model = $this->getModelClass();
        $component = $model::where('identifier', $this->identifier)->first();

        if ($component) {
            $this->content = array_merge($this->getDefaultContent(), $component->content ?? []);
            $this->config = array_merge($this->getDefaultConfig(), $component->config ?? []);
        }
    }

    public function save()
    {
        $this->validate();

        $model = $this->getModelClass();
        $component = $model::updateOrCreate(
            ['identifier' => $this->identifier],
            [
                'content' => $this->content,
                'config' => $this->config,
                'component_name' => static::class,
                'type' => static::getComponentType()
            ]
        );

        $this->emit('componentSaved', [
            'type' => $this->componentType,
            'identifier' => $this->identifier
        ]);

        $this->isEditing = false;

        return $component;
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
        $this->isPreview = false;
    }

    public function togglePreview()
    {
        $this->isPreview = !$this->isPreview;
    }

    protected function getDefaultContent(): array
    {
        return [];
    }

    protected function getDefaultConfig(): array
    {
        return [];
    }

    abstract protected static function getComponentType(): string;
    abstract protected function getModelClass(): string;
}
