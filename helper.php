<?php
/**
 * 様々な関数群
 * Date: 2017/05/21
 * @author takuya
 * @version: 1.0
 */

use KTR\Config;
use KTR\KtrRuntimeException;

/**
 * 環境変数を取得する
 * @param $key
 * @param $default
 * @return array|false|string
 */
function env($key, $default = null)
{
    $value = getenv($key);

    if ($value === false || empty($value)) {
        return $default;
    }

    switch (strtolower($value)) {
        case 'true':
            return true;
        case 'false':
            return false;
        case 'empty':
            return '';
        case 'null':
            return null;
    }

    return $value;
}

/**
 * langファイルから言語を読み込む
 * @param $id
 * @param array $placeHolder
 * @return string
 * @throws KtrRuntimeException
 */
function get_lang($id, $placeHolder = array())
{
    //get lang
    $conf = Config::factory('app.php');
    $path = $conf->appLangPath . '/' . $conf->locale . '.ini';
    if (!file_exists($path)) throw new KtrRuntimeException('Lang ini file not found');
    $config = Config::factory($path);

    $line = $config->$id;

    return vsprintf($line, $placeHolder);
}

/**
 * スネークケースをキャメルケースに変換
 * @param $str
 * @return string
 */
function camelize($str)
{
    return ucfirst(strtr(ucwords(strtr($str, ['_' => ' '])), [' ' => '']));
}

/**
 * スネークケースに変換
 * @param $str
 * @return string
 */
function underscore($str)
{
    return ltrim(strtolower(preg_replace('/[A-Z]/', '_\0', $str)), '_');
}