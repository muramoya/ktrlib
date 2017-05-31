<?php
/**
 * Routing
 * Date: 2016/06/22
 * @author muramoya
 * @version: 1.0
 */

namespace KTR;

use Phalcon\Mvc\Router as PhalconRouter;

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