<?php
/**
 * KTRLib\Logger
 *
 * ログの書き込みを行います。
 *
 * ログの書き込み先はデフォルトではconf/app.phpのappLogPathになります。
 * このクラスをインスタンス化する時の第1引数にパスを指定することで
 * 任意のパスに書き込みが可能です。また第2引数にファイル名を指定することで任意のファイル名で書き込みが可能です。
 * デフォルトではconf/app.phpのdefaultAppLogFileNameになります。
 *
 * <code>
 * $logger = new Logger(); //デフォルトのパス、ファイル名
 *
 * $logger = new Logger('/log/path'); //任意のパス、デフォルトのファイル名
 *
 * $logger = new Logger('/log/path', 'logname.log'); //任意のパス、ファイル名
 *
 * $logger = new Logger(null, 'logname.log'); //デフォルトのパス、任意のファイル名
 * </code>
 *
 * ログレベルのメソッドをコールすることで対応したレベルのログメッセージを1つ書き込みます。
 *
 * <code>
 * $logger->info('message');
 * $logger->notice('message');
 * $logger->warning('message');
 * $logger->error('message');
 * $logger->debug('message');
 * </code>
 *
 * またwriteメソッドでは一度に複数のログメッセージを書き込めます。
 * <code>
 * //全て同じレベルで書き込む場合
 * $logger->write(['msg1', 'msg2']);
 * //または
 * $logger->write([
 *     　　　['msg' =>msg1],
 *          ['msg' =>msg2],
 *      ]);
 *
 * //ログメッセージ個別にレベルを指定する場合
 * $logger->write([
 *          ['msg' => 'msg1', 'level' => Logger::INFO],
 *          ['msg' => 'msg2', 'level' => Logger::NOTICE],
 *      ]);
 * </code>
 *
 * @author muramoya
 * @version: 1.0
 */

namespace KTRLib;

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
     * @param string $filePath
     * @param string $fileName
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
     * 複数のログを書き込みます
     * @param array $contents
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

    /**
     * infoレベルのログを1つ書き込みます
     * @param string $msg
     */
    public function info($msg)
    {
        $this->logger->info($msg);
    }

    /**
     * noticeレベルのログを1つ書き込みます
     * @param string $msg
     */
    public function notice($msg)
    {
        $this->logger->notice($msg);
    }

    /**
     * warningレベルのログを1つ書き込みます
     * @param string $msg
     */
    public function warning($msg)
    {
        $this->logger->warning($msg);
    }

    /**
     * errorレベルのログを1つ書き込みます
     * @param string $msg
     */
    public function error($msg)
    {
        $this->logger->error($msg);
    }

    /**
     * debugレベルのログを1つ書き込みます
     * @param string $msg
     */
    public function debug($msg)
    {
        $this->logger->debug($msg);
    }
}