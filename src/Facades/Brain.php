<?php

namespace HelgeSverre\Brain\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \HelgeSverre\Brain\Brain
 */
class Brain extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \HelgeSverre\Brain\Brain::class;
    }
}
