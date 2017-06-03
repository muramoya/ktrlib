<?php
/**
 * KTRLib\Config
 *
 * 設定ファイルを読み込みます。
 * 拡張子によって返却クラスを変更します。
 *
 * 第1引数にファイル名を指定すると拡張子を自動で判断し
 * PhalconのConfigインスタンスを返却します。拡張子はPHP,Ini,Yml(Yaml)が使用可能です。
 * 指定した設定ファイルが見つからない場合はfalseを返却します。
 * Configの操作はPhalconに則ります。
 *
 * 第1引数の指定方法は2種類あります。デフォルトのconfディレクトリ以下を読み込む方法と
 * パスを指定する方法です。
 *
 * <code>
 * //confディレクトリ以下のファイルを読み込む方法
 * $conf = Config::factory('app.php'); //ファイル名のみを指定します。
 * $line = $conf->foo; //Phalconでは読み込んだ設定ファイルをプロパティ指定で取得できます。
 *
 * //パスを指定する方法
 * $conf = Config::factory('/hoge/bar/piyo.php');
 * $line = $conf->foo;
 * </code>
 *
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
     * @return bool|\Phalcon\Config
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