<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Helpers\SiteSetting;
use App\Models\Page;

class HomeController extends Controller
{
    public function __invoke($page)
    {
        // Obtener la página con sus secciones ordenadas y activas
        $pageData = Page::where('slug', $page)
            ->with(['sections' => function($query) {
                $query->where('is_active', true)
                    ->orderBy('order');
            }])
            ->firstOrFail();

        // Obtener el título traducido o establecer uno por defecto
        $metaTitle = __('Meta Title: ' . $page);
        if($metaTitle == 'Meta Title: ' . $page) {
            $metaTitle = ucfirst(str_replace('_', ' ', $page));
        }

        // Determinar si estamos en modo prueba
        $isTestMode = request()->segment(1) === 'test';

        // Seleccionar la vista basada en el modo
        $view = $isTestMode ? 'pages.test' : 'pages.' . $page;

        return view($view, [
            'info' => $this->pageInfo(),
            'metaTitle' => $metaTitle,
            'page' => $pageData
        ]);
    }

    public function index(){
        return redirect()->route('page',['page' => '_home']);
    }

    public function pageInfo() {
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
