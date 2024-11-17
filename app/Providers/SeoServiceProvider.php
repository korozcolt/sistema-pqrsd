<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Helpers\SiteSetting;

class SeoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('layouts.page', function ($view) {
            $currentRoute = request()->route();
            $page = $currentRoute ? $currentRoute->parameter('page') : '_home';

            $seoConfig = config('site.seo.pages.' . $page, config('site.seo.default'));
            $companyInfo = config('site.company');

            $seoData = [
                'title' => $seoConfig['title'],
                'description' => $seoConfig['description'],
                'keywords' => $seoConfig['keywords'] ?? config('site.seo.default.keywords'),
                'image' => $seoConfig['image'] ?? config('site.seo.default.image'),
                'url' => url()->current(),
            ];

            $info = (object) [
                'title' => $companyInfo['name'],
                'logo' => config('site.assets.logo'),
                'favicon' => config('site.assets.favicon'),
                'email' => $companyInfo['contact']['emails']['main'],
                'email2' => $companyInfo['contact']['emails']['secondary'],
                'phone' => $companyInfo['contact']['phones']['main'],
                'phone2' => $companyInfo['contact']['phones']['secondary'],
                'address' => $companyInfo['contact']['address'],
                'facebook' => config('site.social.facebook'),
                'twitter' => config('site.social.twitter'),
                'instagram' => config('site.social.instagram'),
                'linkedin' => config('site.social.linkedin'),
            ];

            $view->with('seo', $seoData);
            $view->with('info', $info);
        });
    }
}
