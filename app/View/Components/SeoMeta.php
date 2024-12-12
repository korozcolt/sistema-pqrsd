<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Str;
use App\Models\Page;
use App\Helpers\SiteSetting;

class SeoMeta extends Component
{
    public $title;
    public $description;
    public $keywords;
    public $image;
    public $type;
    public $url;
    public $page;
    public $canonical;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $title = null,
        $description = null,
        $keywords = null,
        $image = null,
        $type = 'website',
        $url = null,
        $canonical = null,
        Page $page = null
    ) {
        $this->page = $page;
        $this->title = $this->generateTitle($title);
        $this->description = $this->generateDescription($description);
        $this->keywords = $this->generateKeywords($keywords);
        $this->image = $this->generateImage($image);
        $this->type = $type;
        $this->url = $this->generateUrl($url);
        $this->canonical = $this->generateCanonical($canonical);
    }

    /**
     * Generate optimized title
     */
    protected function generateTitle($title = null): string
    {
        // Si hay un título específico, úsalo
        if ($title) {
            return $title . ' | ' . SiteSetting::getTitle();
        }

        // Si es una página conocida en la configuración
        if ($this->page) {
            $pagePath = $this->page->slug ?? '_home';
            $configTitle = SiteSetting::get("seo.pages.{$pagePath}.title");
            if ($configTitle) {
                return $configTitle;
            }
        }

        // Título por defecto desde la configuración
        return SiteSetting::get('seo.default.title');
    }

    /**
     * Generate meta description
     */
    protected function generateDescription($description = null): string
    {
        if ($description) {
            return Str::limit($description, 155);
        }

        if ($this->page) {
            // Buscar descripción específica de la página en la configuración
            $pagePath = $this->page->slug ?? '_home';
            $configDesc = SiteSetting::get("seo.pages.{$pagePath}.description");
            if ($configDesc) {
                return Str::limit($configDesc, 155);
            }
        }

        return Str::limit(SiteSetting::get('seo.default.description'), 155);
    }

    /**
     * Generate keywords
     */
    protected function generateKeywords($keywords = null): string
    {
        if ($keywords) {
            return $keywords;
        }

        if ($this->page && $this->page->meta_keywords) {
            return $this->page->meta_keywords;
        }

        return SiteSetting::get('seo.default.keywords');
    }

    /**
     * Generate social media image
     */
    protected function generateImage($image = null): string
    {
        if ($image) {
            return asset($image);
        }

        if ($this->page && isset($this->page->og_data['image'])) {
            return asset($this->page->og_data['image']);
        }

        return asset(SiteSetting::get('seo.default.image'));
    }

    /**
     * Get JSON-LD schema for the page
     */
    public function getSchema(): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => SiteSetting::getTitle(),
            'url' => $this->url,
            'logo' => asset(SiteSetting::getLogo()),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => SiteSetting::getPhone(),
                'email' => SiteSetting::getEmail(),
                'contactType' => 'customer service',
                'areaServed' => 'CO',
                'availableLanguage' => ['Spanish']
            ],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => SiteSetting::getAddress(),
                'addressCountry' => 'CO'
            ],
            'sameAs' => [
                SiteSetting::getSocial('facebook'),
                SiteSetting::getSocial('twitter'),
                SiteSetting::getSocial('instagram'),
                SiteSetting::getSocial('linkedin'),
            ]
        ];

        // Agregar datos de la empresa
        $schema['foundingDate'] = SiteSetting::get('company.since');
        $schema['taxID'] = SiteSetting::get('company.nit');

        // Si es una página específica, agregar schema adicional
        if ($this->page) {
            $schema['mainEntityOfPage'] = [
                '@type' => 'WebPage',
                '@id' => $this->url,
                'name' => $this->title,
                'description' => $this->description,
                'inLanguage' => 'es-CO'
            ];
        }

        return $schema;
    }

    /**
     * Generate canonical URL
     */
    protected function generateCanonical($canonical = null): string
    {
        if ($canonical) {
            return $canonical;
        }

        return $this->generateUrl();
    }

    /**
     * Generate current URL
     */
    protected function generateUrl($url = null): string
    {
        if ($url) {
            return $url;
        }

        return url()->current();
    }

    /**
     * Get Twitter card type
     */
    public function getTwitterCardType(): string
    {
        return $this->image ? 'summary_large_image' : 'summary';
    }

    /**
     * Get language attributes
     */
    public function getLangAttributes(): array
    {
        return [
            'lang' => app()->getLocale(),
            'dir' => 'ltr'
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.seo-meta', [
            'schema' => $this->getSchema(),
            'twitterCardType' => $this->getTwitterCardType(),
            'langAttributes' => $this->getLangAttributes(),
            'company' => [
                'name' => SiteSetting::getTitle(),
                'since' => SiteSetting::get('company.since'),
                'nit' => SiteSetting::get('company.nit'),
                'phone' => SiteSetting::getPhone(),
                'email' => SiteSetting::getEmail(),
                'address' => SiteSetting::getAddress()
            ]
        ]);
    }
}
