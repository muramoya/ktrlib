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

    /**
     * Router constructor.
     */
    public function __construct() {
        $this->router = new PhalconRouter();
        $conf = Config::factory('routing.php');
        $this->conf = $conf->toArray();
    }

    /**
     * conf以下のrouting設定に沿ってルーティングをする
     */
    public function routing() {
        foreach ($this->conf as $url => $settings)
        {
            if (isset($settings['method'])) {
                $this->router->add($url, $settings['path'])->via(array_walk(
                    $settings['method'],
                    function(&$val) {$val = strtoupper($val);}
                ));
            } else {
                $this->router->add($url, $settings['path']);
            }
        }
    }
}