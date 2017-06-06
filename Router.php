<?php

namespace KTRLib;

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\Collection;
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
     * @var Micro\Collection
     */
    private $router;
    private $conf;
    private $app;

    /**
     * Router constructor.
     * @param Micro $app
     */
    public function __construct(Micro $app)
    {
        $this->app = $app;
        $conf = Config::factory('routing.php');
        $this->conf = $conf->toArray();
    }

    private function init()
    {
        $this->router = new Collection();
    }

    /**
     * conf以下のrouting設定に沿ってルーティングをする
     * @return PhalconRouter
     */
    public function routing()
    {
        $nameSpace = Config::factory('app.php')->appNameSpace . '\\Apps\\Controllers\\';
        foreach ($this->conf as $baseUrl => $settings)
        {
            $this->init();
            $controller = $nameSpace . $settings['controller'];

            $this->router->setHandler(new $controller);
            $this->router->setPrefix($baseUrl);
            unset($settings['controller']);
            $this->setAction($settings);
        }
        $this->app->mount($this->router);

        $this->setNotFoundAction();
    }

    /**
     * @param array $settings
     * @throws KtrRuntimeException
     */
    private function setAction($settings)
    {
        foreach ($settings as $method => $actSet)
        {
            foreach ($actSet as $set)
            {
                $pattern = isset($set['url']) ? $set['url'] : '/';
                $action = $set['action'];
                switch ($method)
                {
                    case 'get':
                    case 'GET':
                        $this->router->get($pattern, $action);
                        break;
                    case 'post':
                    case 'POST':
                        $this->router->post($pattern, $action);
                        break;
                    case 'put':
                    case 'PUT':
                        $this->router->put($pattern, $action);
                        break;
                    case 'delete':
                    case 'DELETE':
                        $this->router->delete($pattern, $action);
                        break;
                    default:
                        throw new KtrRuntimeException('Invalid routing method setting');
                }
            }
        }
    }

    private function setNotFoundAction()
    {
        $app = $this->app;
        $this->app->notFound(function() use ($app) {
            $app->response->setStatusCode(404, "Not Found")->sendHeaders();
        });
    }
}