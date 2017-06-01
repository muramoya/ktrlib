<?php
/**
 * 設定ファイル読み込みファクトリ
 * 拡張子によって返却クラスを変更する
 * Date: 2017/05/21
 * @author muramoya
 * @version: 1.0
 */

namespace KTRLib;

use Phalcon\Config\Adapter\Php as ConfigPhp;
use Phalcon\Config\Adapter\Ini as ConfigIni;
use Phalcon\Config\Adapter\Yaml as ConfigYml;

class Config
{

    /**
     * ファクトリメソッド
     * @param $file
     * @return bool|ConfigIni|ConfigPhp|ConfigYml
     */
    public static function factory($file) {
        if (strpos($file, '/') === false) {
            $file = realpath(APP_BASE_PATH.'/conf') . '/' . $file;
        }

        if (!file_exists($file)) return false;

        $explodePath = explode('/', $file);
        $fileName = end($explodePath);

        $explodeExt = explode('.', $fileName);

        switch (end($explodeExt)) {
            case 'ini':
                return new ConfigIni($file);
            case 'php':
                return new ConfigPhp($file);
            case 'yml':
            case 'yaml':
                return new ConfigYml($file);
            default:
                return false;
        }
    }
}