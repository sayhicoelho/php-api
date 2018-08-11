<?php

namespace Core;

use Core\Exceptions\InvalidActionException;

class Router
{
    /**
     * The router prefix.
     *
     * @var string
     */
    private $prefix;

    /**
     * The routes.
     *
     * @var array
     */
    private $routes = [];

    /**
     * The actions.
     *
     * @var array
     */
    private $actions = [];

    /**
     * The URL segments.
     *
     * @var array
     */
    private $segments = [];

    /**
     * The URL segments length.
     *
     * @var int
     */
    private $segments_count;

    /**
     * The Request instance.
     *
     * @var \Core\Request
     */
    private $request;

    /**
     * The Middleware instance.
     *
     * @var \Core\Middleware
     */
    private $middleware;

    /**
     * The router middlewares.
     *
     * @var array
     */
    private $middlewares = [];

    /**
     * The controllers namespace.
     *
     * @var string
     */
    private $namespace = 'App\\Http\\Controllers';

    /**
     * The constructor.
     *
     * @param  \Core\Request     $request
     * @param  \Core\Middleware  $middleware
     * @return void
     */
    public function __construct (Request $request, Middleware $middleware)
    {
        $this->request = $request;

        $this->middleware = $middleware;
    }

    /**
     * Add a route to router collection.
     *
     * @param  string           $method
     * @param  string           $uri
     * @param  \Closure|string  $action
     * @return void
     */
    private function addRoute ($method, $uri, $action)
    {
        if (!is_null($this->prefix))
        {
            $uri = explode('/', $uri);
            array_splice($uri, 1, 0, $this->prefix);
            $uri = implode($uri, '/');
        }

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action,
            'middlewares' => $this->middlewares,
        ];
    }

    /**
     * Set locale if present in URL.
     *
     * @param  string  $uri
     * @return array
     */
    private function setLocale ($uri)
    {
        if (isset($uri[1]) && in_array($uri[1], Config::get('languages')))
        {
            App::setLocale($uri[1]);

            unset($uri[1]);

            $uri = array_values($uri);
        }

        return $uri;
    }

    /**
     * Check middlewares and run if it's present on route.
     *
     * @param  array  $middlewares
     * @return \Core\Response|void
     */
    private function checkMiddlewares (array $middlewares)
    {
        foreach ($middlewares as $middleware => $params)
        {
            if (($response = $this->middleware->run($this->request, $middleware, $params)) instanceof Response)
            {
                return $response;
            }
        }
    }

    /**
     * Prepare the actions to be performed.
     *
     * @param  string           $method
     * @param  \Closure|string  $action
     * @param  array            $middlewares
     * @param  array            $params
     * @return void
     */
    private function prepareAction ($method, $handler, array $middlewares = [], array $params = [])
    {
        $this->actions[] = compact('method', 'handler', 'middlewares', 'params');
    }

    /**
     * Perform the action.
     *
     * @return \Core\Response|void
     */
    private function performAction ()
    {
        if (count($this->actions) > 0)
        {
            foreach ($this->actions as $action)
            {
                if ($action['method'] === $_SERVER['REQUEST_METHOD'])
                {
                    if (!$response = $this->checkMiddlewares($action['middlewares']))
                    {
                        $response = $this->handleAction($action);
                    }

                    return $response;
                }
            }

            $this->catchBadRequest();
        }
        else
        {
            $this->catchNotFound();
        }
    }

    /**
     * Handle the action.
     *
     * @param  \Closure|string  $action
     * @return \Core\Response|void
     */
    private function handleAction ($action)
    {
        array_unshift($action['params'], $this->request);

        if (is_callable($action['handler']))
        {
            return call_user_func_array($action['handler'], $action['params']);
        }
        else
        {
            $controller = explode('@', $action['handler']);

            $class = "{$this->namespace}\\{$controller[0]}";
            $method = $controller[1];

            if (class_exists($class) && method_exists($class, $method))
            {
                return call_user_func_array([new $class, $method], $action['params']);
            }
            else
            {
                throw new InvalidActionException("Method {$method} from {$class} does not exist.");
            }
        }
    }

    /**
     * Parse a specified URI.
     *
     * @param  string  $uri
     * @return array
     */
    private function parseUri ($uri)
    {
        $uri = explode('?', $uri)[0];
        $uri = explode('/', $uri);
        $uri = array_filter($uri);
        $uri = array_values($uri);

        array_unshift($uri, '/');

        return $uri;
    }

    /**
     * Parse the request URI.
     *
     * @param  string  $uri
     * @return array
     */
    private function parseRequestUri ($uri)
    {
        $uri = $this->parseUri($uri);

        $uri = $this->setLocale($uri);

        return $uri;
    }

    /**
     * Parse the route URI.
     *
     * @param  string  $uri
     * @return array
     */
    private function parseRouteUri ($uri)
    {
        $uri = $this->parseUri($uri);

        return $uri;
    }

    /**
     * Catch Not Found.
     *
     * @return \Core\Response
     */
    private function catchNotFound ()
    {
        Response::error(404, 'Not Found');
    }

    /**
     * Catch Bad Request.
     *
     * @return \Core\Response
     */
    private function catchBadRequest ()
    {
        Response::error(400, 'Bad Request');
    }

    /**
     * Add route using GET method.
     *
     * @param  string           $uri
     * @param  \Closure|string  $action
     * @return void
     */
    public function get ($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
    }

    /**
     * Add route using POST method.
     *
     * @param  string           $uri
     * @param  \Closure|string  $action
     * @return void
     */
    public function post ($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);
    }

    /**
     * Add route using PUT method.
     *
     * @param  string           $uri
     * @param  \Closure|string  $action
     * @return void
     */
    public function put ($uri, $action)
    {
        $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Add route using PATCH method.
     *
     * @param  string           $uri
     * @param  \Closure|string  $action
     * @return void
     */
    public function patch ($uri, $action)
    {
        $this->addRoute('PATCH', $uri, $action);
    }

    /**
     * Add route using DELETE method.
     *
     * @param  string           $uri
     * @param  \Closure|string  $action
     * @return void
     */
    public function delete ($uri, $action)
    {
        $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Add a prefix to router.
     *
     * @param  string  $segment
     * @return void
     */
    public function prefix ($segment)
    {
        $this->prefix = $segment;
    }

    /**
     * Reset the router prefix.
     *
     * @return void
     */
    public function resetPrefix ()
    {
        $this->prefix = null;
    }

    /**
     * Add a middleware to router.
     *
     * @param  string  $middleware
     * @param  array   $params
     * @return void
     */
    public function middleware ($middleware, array $params = [])
    {
        $this->middlewares[$middleware] = $params;
    }

    /**
     * Replace current router middlewares.
     *
     * @param  string  $middleware
     * @param  array   $params
     * @return void
     */
    public function replaceMiddleware ($middleware, array $params = [])
    {
        $this->middlewares = [];

        $this->middleware($middleware, $params);
    }

    /**
     * Remove router middlewares.
     *
     * @param  array  $middlewares
     * @return void
     */
    public function unsetMiddlewares (...$middlewares)
    {
        foreach ($middlewares as $middleware)
        {
            unset($this->middlewares[$middleware]);
        }
    }

    /**
     * Reset the router middlewares.
     *
     * @return void
     */
    public function resetMiddlewares ()
    {
        $this->middlewares = [];
    }

    /**
     * Run the router engine.
     *
     * @return \Core\Response|void
     */
    public function run ()
    {
        $this->segments = $this->parseRequestUri($_SERVER['REQUEST_URI']);
        $this->segments_count = count($this->segments);

        foreach ($this->routes as $route)
        {
            $uri = $this->parseRouteUri($route['uri']);

            $params = [];

            if (count($uri) === $this->segments_count)
            {
                for ($i = 0; $i < $this->segments_count; $i++)
                {
                    $is_param = $uri[$i][0] === ':';

                    if ($is_param)
                    {
                        $params[] = $this->segments[$i];
                    }
                    else if ($this->segments[$i] !== $uri[$i])
                    {
                        continue 2;
                    }
                }

                $this->prepareAction($route['method'], $route['action'], $route['middlewares'], $params);
            }
        }

        return $this->performAction();
    }
}
