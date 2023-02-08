<?php

namespace Jaulz\Sequentia\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Jaulz\Sequentia\Sequentia
 */
class Sequentia extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Jaulz\Sequentia\Sequentia::class;
    }
}