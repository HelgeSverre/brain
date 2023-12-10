<?php

namespace HelgeSverre\AI\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \HelgeSverre\AI\AI
 */
class AI extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \HelgeSverre\AI\AI::class;
    }
}
