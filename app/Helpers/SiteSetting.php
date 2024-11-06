<?php

namespace App\Helpers;

use Illuminate\Support\Number;

class SiteSetting
{

    public static function getLogo()
    {
        $logo = config('site.logo');
        return $logo;
    }

    public static function getFavicon()
    {
        $favicon = config('site.favicon');
        return $favicon;
    }

    public static function getTitle()
    {
        $title = config('site.name');
        return $title;
    }

    //email
    public static function getEmail()
    {
        $email = config('site.email');
        return $email;
    }

    //phone
    public static function getPhone()
    {
        $phone = config('site.phone');
        return $phone;
    }

    //phone2
    public static function getPhone2()
    {
        $phone2 = config('site.phone2');
        return $phone2;
    }

    //address
    public static function getAddress()
    {
        $address = config('site.address');
        return $address;
    }

    //email2
    public static function getEmail2()
    {
        $email2 = config('site.email2');
        return $email2;
    }

}
