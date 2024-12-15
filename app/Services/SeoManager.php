<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Page;
use App\Models\Section;

class SeoManager
{
    protected $page;
    protected $defaultMeta;

    public function __construct()
    {
        $this->defaultMeta = [
            'title' => config('app.name'),
            'description' => config('site.company.description'),
            'robots' => 'index,follow',
            'og_type' => 'website',
            'twitter_card' => 'summary_large_image'
        ];
    }

    public function setPage(Page $page)
    {
        $this->page = $page;
        return $this;
    }

    public function generateMetaTags(): array
    {
        if (!$this->page) {
            return $this->defaultMeta;
        }

        return [
            'title' => $this->page->title . ' | ' . config('app.name'),
            'description' => $this->page->meta_description ?? $this->defaultMeta['description'],
            'keywords' => $this->page->meta_keywords,
            'og_title' => $this->page->title,
            'og_description' => $this->page->meta_description,
            'og_image' => $this->page->og_data['image'] ?? null,
            'canonical' => url($this->page->slug),
            'robots' => $this->page->searchable ? 'index,follow' : 'noindex,nofollow',
            'schema' => $this->generateSchema()
        ];
    }

    protected function generateSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('app.name'),
            'url' => config('app.url'),
            'logo' => asset('images/logo.png'),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => config('site.company.contact.phones.main'),
                'contactType' => 'customer service'
            ]
        ];
    }

    public function generateSitemap(): string
    {
        $pages = Page::where('status', 'active')
            ->where('searchable', true)
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($pages as $page) {
            $xml .= '<url>';
            $xml .= '<loc>' . url($page->slug) . '</loc>';
            $xml .= '<lastmod>' . $page->updated_at->toW3cString() . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>' . ($page->slug === '_home' ? '1.0' : '0.8') . '</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';
        return $xml;
    }
}
