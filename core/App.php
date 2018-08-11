<?php

namespace Core;

class App
{
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
     * The Response instance.
     *
     * @var \Core\Response
     */
    private $response;

    /**
     * The constructor.
     *
     * @return void
     */
    public function __construct ()
    {
        Env::init();

        if (Config::get('app', 'env') === 'production')
        {
            $this->setDebug(false);
        }
        else
        {
            $this->setDebug(Config::get('app', 'debug'));
        }

        $this->setTimezone();

        $this->request = new Request;
    }

    /**
     * Set the application's debug level.
     *
     * @param  boolean  $debug
     * @return void
     */
    private function setDebug ($debug)
    {
        if ($debug)
        {
            error_reporting(E_ALL);
        }
        else
        {
            error_reporting(false);
        }
    }

    /**
     * Set the application's default timezone.
     *
     * @return void
     */
    private function setTimezone ()
    {
        date_default_timezone_set(Config::get('app', 'timezone'));
    }

    /**
     * Set the application's language.
     *
     * @param  string  $lang
     * @return void
     */
    public static function setLocale ($lang)
    {
        Config::set('app', 'lang', $lang);
    }

    /**
     * Get the running environment.
     *
     * @return string
     */
    public static function environment ()
    {
        return Config::get('app', 'env');
    }

    /**
     * Set the application's middlewares.
     *
     * @param  array  $middleware
     * @return void
     */
    public function routeMiddleware (array $middleware)
    {
        $this->middleware = new Middleware($middleware);
    }

    /**
     * Print the application's output.
     *
     * @return void
     */
    public function run ()
    {
        $router = new Router($this->request, $this->middleware);

        require_once routes_dir('web.php');

        $response = $router->run();

        echo $response->get();
    }
}
