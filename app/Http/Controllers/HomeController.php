<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Helpers\SiteSetting;

class HomeController extends Controller
{
    public function __invoke($page)
    {
        // Obtenemos el título traducido o establecemos uno por defecto
        $metaTitle = __('Meta Title: ' . $page);
        if($metaTitle == 'Meta Title: ' . $page) {
            $metaTitle = ucfirst(str_replace('_', ' ', $page)); // Convertimos _home a Home
        }

        return view('pages.'.$page,[
            'info' => $this->pageInfo(),
            'metaTitle' => $metaTitle
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
