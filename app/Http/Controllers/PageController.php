<?php

// app/Http/Controllers/PageController.php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Helpers\SiteSetting;

class PageController extends Controller
{
    public function show($page)
    {
        // Buscamos la página en la base de datos
        $template = Page::with(['sections' => function($query) {
            $query->orderBy('order');
        }])->where('slug', $page)
          ->where('status', 'active')
          ->firstOrFail();

        // Mantenemos el mismo sistema de SEO que tienes configurado
        $metaTitle = __('Meta Title: ' . $page);
        if($metaTitle == 'Meta Title: ' . $page) {
            $metaTitle = ucfirst(str_replace('_', ' ', $page));
        }

        return view('test', [
            'page' => $template,
            'info' => $this->pageInfo(),
            'metaTitle' => $metaTitle
        ]);
    }

    private function pageInfo()
    {
        return (object) [
            'title' => SiteSetting::getTitle(),
            'logo' => SiteSetting::getLogo(),
            'favicon' => SiteSetting::getFavicon(),
            'email' => SiteSetting::getEmail(),
            'phone' => SiteSetting::getPhone(),
            'phone2' => SiteSetting::getPhone2(),
            'address' => SiteSetting::getAddress(),
            'email2' => SiteSetting::getEmail2(),
        ];
    }
}
