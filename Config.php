<?php
/**
 * 設定ファイル読み込みファクトリ
 * 拡張子によって返却クラスを変更する
 * Date: 2017/05/21
 * @author takuya
 * @version: 1.0
 */

namespace KTR;

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
            $file = realpath(__DIR__.'/../conf') . '/' . $file;
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