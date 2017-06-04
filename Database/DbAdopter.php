<?php
namespace KTRLib\Database;

use KTRLib\Config;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Db\Adapter\Pdo\Postgresql;
use Phalcon\Db\Adapter\Pdo\Sqlite;

/**
 * データベース接続クラス
 *
 * このクラスはPhalconDiに登録されています。
 * そのため、Diを取得すればこのクラスが利用できます。
 *
 * <pre><code class="language-php">
 * $di = new Di();
 * $db = $di->get('db');
 * </code></pre>
 *
 * もしくは直接インスタンス化することも可能です。
 *
 * <pre><code class="language-php">
 * $db = DbAdopter::factory();
 * </code></pre>
 *
 * データベースの操作はPhalconに則っています。
 *
 * 接続するDBドライバは.envで設定します。通常.envはアプリケーションのルートディレクトリ直下に置かれています。
 * 現在mysql,postgresql,sqlliteが使用可能です。
 * デフォルトではデータベースの接続にはこのクラスが格納されているディレクトリ以下の設定ファイルを読み込みます。
 * ファイル名はmysql.php,postgresql.php,sqllite.phpです。
 *
 * アプリケーションの設定ファイルディレクトリに同名のファイルを配置すれば独自の設定が可能です。
 * 設定ファイルディレクトリはアプリケーションルートディレクトリ/confです。
 *
 * @author muramoya
 * @version: 1.1.0
 */
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