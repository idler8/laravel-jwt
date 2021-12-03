<?php

namespace Idler8\Laravel;

use \Idler8\Laravel\Library\Core;

class Jwt
{
    public function __call($method, $parameters)
    {
        return (new Core)->$method(...$parameters);
    }
    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }
}
