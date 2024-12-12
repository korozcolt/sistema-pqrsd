<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;

class SiteSetting
{
    public static function get($key, $default = null)
    {
        return Config::get('site.'.$key, $default);
    }

    public static function getLogo()
    {
        return static::get('assets.logo');
    }

    public static function getFavicon()
    {
        return static::get('assets.favicon');
    }

    public static function getTitle()
    {
        return static::get('company.name');
    }

    public static function getEmail()
    {
        return static::get('company.contact.emails.main');
    }

    public static function getEmail2()
    {
        return static::get('company.contact.emails.secondary');
    }

    public static function getPhone()
    {
        return static::get('company.contact.phones.main');
    }

    public static function getPhone2()
    {
        return static::get('company.contact.phones.secondary');
    }

    public static function getAddress()
    {
        return static::get('company.contact.address');
    }

    public static function getSocial($key)
    {
        return static::get('social.'.$key);
    }
}
