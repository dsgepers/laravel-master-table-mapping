<?php

namespace Schepeis\Mapping\Facade;

use Illuminate\Support\Facades\Facade;

class Mapper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'schepeis-mapper';
    }
}
