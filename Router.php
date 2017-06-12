<?php

namespace KTRLib;

use Phalcon\Mvc\Router as PhalconRouter;

/**
 * ルーティングを行います。
 *
 * @author muramoya
 * @version: 1.0
 */
class Router
{
    /**
     * @var PhalconRouter
     */
    private $router;
    private $conf;
    private $namespace;

    /**
     * Router constructor.
     */
    public function __construct() {
        $this->router = new PhalconRouter(false);
        $conf = Config::factory('routing.php');
        $this->conf = $conf->toArray();
        $this->namespace = Config::factory('app.php')->appNameSpace;
    }

    /**
     * conf以下のrouting設定に沿ってルーティングをする
     */
    public function routing() {
        foreach ($this->conf as $baseUrl => $settings)
        {
            $setting['namespace'] = $this->namespace . '\\Apps\\Controllers\\';
            $setting['controller'] =  $settings['controller'];
            foreach ($settings['actions'] as $actSet)
            {
                $url = isset($actSet['url']) ? $baseUrl . $actSet['url'] : $baseUrl;
                $setting['action'] = $actSet['action'];
                if (isset($actSet['method'])) {
                    if(is_array($actSet['method']))
                    {
                        foreach ($actSet['method'] as $method)
                        {
                            $methods[] = strtoupper($method);
                        }
                    }
                    else
                    {
                        $methods[] = strtoupper($actSet['method']);
                    }
                    $this->router->add($url, $setting)->via($methods);
                } else {
                    $this->router->add($url, $setting);
                }
            }
        }

        $this->setNotFoundAction();

        return $this->router;
    }

    private function setNotFoundAction()
    {
        if(isset(($this->conf)['notfound']))
        {
            $setting['namespace'] = $this->namespace . '\\Apps\\Controllers\\';
            $setting['controller'] = ($this->conf)['notfound']['controller'];
            $setting['action'] = ($this->conf)['notfound']['action'];
        }
        else
        {
            $setting['namespace'] = $this->namespace . '\\Apps\\Controllers\\';
            $setting['controller'] = 'notfound';
            $setting['action'] = 'index';
        }

        $this->router->notFound($setting);
    }
}