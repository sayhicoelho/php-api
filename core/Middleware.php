<?php

namespace Core;

use Core\Exceptions\InvalidMiddlewareException;

class Middleware
{
    /**
     * The application's middlewares.
     *
     * @var array
     */
    private $middlewares;

    /**
     * The constructor.
     *
     * @param  array  $middlewares
     * @return void
     */
    public function __construct (array $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * Handle the middleware.
     *
     * @param  \Core\Request  $request
     * @param  string         $middleware
     * @param  array          $params
     * @return \Core\Response|void
     */
    public function run (Request $request, $middleware, array $params = [])
    {
        if (array_key_exists($middleware, $this->middlewares))
        {
            $class = $this->middlewares[$middleware];

            array_unshift($params, $request);

            return call_user_func_array([new $class, 'handle'], $params);
        }
        else
        {
            throw new InvalidMiddlewareException("The route middleware \"{$middleware}\" does not exist.");
        }
    }
}
