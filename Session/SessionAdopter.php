<?php
/**
 * セッションクラス管理
 * Date: 2017/05/22
 * @author takuya
 * @version: 1.0
 */

namespace KTR\Session;

use KTR\Config;
use Phalcon\Session\Adapter\Files;
use Phalcon\Session\Adapter\Libmemcached;
use Phalcon\Session\Adapter\Redis;

class SessionAdopter
{
    /**
     * ファクトリメソッド
     * @return bool|Libmemcached|Redis|Files
     */
    public static function factory() {
        $driver = env('SESSION_DRIVER', 'file');

        //confディレクトリ以下に設定ファイルがあればそれを設定値として読み込む
        $conf = Config::factory($driver . '.php');
        if ((bool)$conf) {
            $settings = $conf->toArray();
        } elseif (file_exists("./{$driver}.php")) {
            $settings = require_once $driver . '.php';
        } else {
            $settings = '';
        }

        switch ($driver) {
            case 'memcached':
                $session = new Libmemcached($settings);
                break;
            case 'redis':
                $session = new Redis($settings);
                break;
            case 'file':
                $session = new Files();
                break;
            default:
                return false;
        }

        $session->start();
        return $session;
    }
}