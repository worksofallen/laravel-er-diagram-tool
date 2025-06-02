<?php

namespace Worksofallen\LaravelERD;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Worksofallen\LaravelERD\LaravelERD
 */
class LaravelERDFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-erd';
    }
}
