<?php
/**
 * Bootstrap
 * Date: 2016/06/25
 * @author muramoya
 * @version: 1.0
 */

namespace KTR;

use KTR\Libs\Development\Debug;
use Phalcon\Mvc\Micro;

class Bootstrap
{
    /**
     * @var Micro
     */
    private $app;

    public function __construct($di) {
        $this->app = new Micro($di);
    }

    public function dispatch() {
        try {
            $router = new Router();
            //ルーティングに沿ったcontrollerを設定
            $router->routing();
            $this->setNotFoundAction();
            $this->app->handle();
        }
        catch(\Exception $e) {
            throw $e;
        }
        catch (\Error $e) {
            throw $e;
        }
    }

    private function setNotFoundAction() {
        $app = $this->app;
        $this->app->notFound(function() use ($app) {
            $app->response->setStatusCode(404, "Not Found")->sendHeaders();
        });
    }
}