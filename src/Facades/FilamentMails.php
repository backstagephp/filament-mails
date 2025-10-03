<?php

namespace Backstage\FilamentMails\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Backstage\FilamentMails\FilamentMails
 */
class FilamentMails extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Backstage\FilamentMails\FilamentMails::class;
    }
}
