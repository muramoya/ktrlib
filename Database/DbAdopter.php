<?php
/**
 * データベースクラス管理
 * Date: 2017/05/22
 * @author takuya
 * @version: 1.0
 */

namespace KTR\Database;

use KTR\Config;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Db\Adapter\Pdo\Postgresql;
use Phalcon\Db\Adapter\Pdo\Sqlite;

class DbAdopter
{
    /**
     * ファクトリメソッド
     * @return bool|Mysql|Postgresql|Sqlite
     */
    public static function factory() {
        $driver = env('DATABASE_DRIVER', 'mysql');

        //confディレクトリ以下に設定ファイルがあればそれを設定値として読み込む
        $conf = Config::factory($driver . '.php');
        if ((bool)$conf) {
            $settings = $conf->toArray();
        } elseif (file_exists(__DIR__."/{$driver}.php")) {
            $coreConf = Config::factory(__DIR__."/{$driver}.php");
            $settings = $coreConf->toArray();
        } else {
            $settings = '';
        }
        
        switch ($driver) {
            case 'mysql':
                return new Mysql($settings);
                break;
            case 'postresql':
                return new Postgresql($settings);
                break;
            case 'sqllite':
                return new Sqlite($settings);
                break;
            default:
                return false;
        }
    }
}