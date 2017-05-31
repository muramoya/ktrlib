<?php
/**
 * Logger
 * Date: 2016/06/25
 * @author muramoya
 * @version: 1.0
 */

namespace KTR;

use Phalcon\Http\Request;
use Phalcon\Logger\Adapter\File;
use Phalcon\Logger\Formatter\Line as LogFormatter;

class Logger
{
    /**
     * @var File
     */
    private $logger;

    const INFO = 'info';
    const NOTICE = 'notice';
    const WARNING = 'warning';
    const ERROR = 'error';
    const DEBUG = 'debug';

    /**
     * インスタンスを生成
     * Logger constructor.
     * @param null $fileName ファイル名を指定。デフォルトはmain設定ファイルのlog_file_format
     */
    public function __construct($fileName = null) {
        $conf = Config::factory('app.php');

        if ((bool)$fileName) {
            $logFile =  $conf->appLogPath . '/' . $fileName;
        } else {
            $logFile = $conf->appLogPath . '/' . $conf->defaultAppLogFileName;
        }
        $this->logger = new File($logFile);

        $req = new Request();
        $uri = $req->getURI();
        $this->logger->setFormatter(new LogFormatter("[%date%][$uri][%type%] %message%"));
    }

    /**
     * 複数のログを書き込む
     * @param array $contents
     *   全て同じレベルで書き込む場合
     *      $contents = [
     *          msg1,msg2
     *      ]
     *      or
     *      $contents = [
     *          [msg =>msg1],
     *          [msg =>msg2]
     *      ]
     *    個別にレベルを指定する場合
     *      $contents = [
     *          [msg => msg1, level => notice],
     *          [msg => msg2, level => warning],
     *      ]
     * @param null $level $contentsの子要素にlevelを指定しない場合はかならず指定
     */
    public function write(array $contents, $level = null) {
        $this->logger->begin();
        foreach ($contents as $content) {
            if (!is_array($content) || !isset($content['level'])) {
                if (is_null($level)) {
                    $this->warning('No log level set');
                    $this->info($content);
                } else {
                    $this->$level($content);
                }
            } else {
                $level = $content['level'];
                $this->$level($content['msg']);
            }
        }
        $this->logger->commit();
    }

    /*
     * ↓ログ書き込み(単発)
     ********************************/
    
    public function info($msg)
    {
        $this->logger->info($msg);
    }

    public function notice($msg)
    {
        $this->logger->notice($msg);
    }

    public function warning($msg)
    {
        $this->logger->warning($msg);
    }

    public function error($msg)
    {
        $this->logger->error($msg);
    }

    public function debug($msg)
    {
        $this->logger->debug($msg);
    }
}