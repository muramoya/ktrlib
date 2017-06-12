<?php
/**
 * コントローラ抽象クラス
 * @author: muramoya
 */

namespace KTRLib;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;

abstract class AbstractController extends Controller
{
    /**
     * パラメータを取得します。
     * リクエストメソッドに応じて返却されます。
     * GETメソッドの場合はurlのパラメータとGETパラメータを混ぜて返却します。
     * 個別で取得したい場合はそれぞれ
     *
     * <pre>
     * <code class="language-php">
     * $this->dispatcher->getParams();
     * $this->request->getQuery();
     * </cpde>
     * </pre>
     *
     * で取得してください。dispatcherとrequestはDIによる組み込みのサービスです。
     * @return bool|mixed
     */
    public function getParams()
    {
        switch ($this->request->getMethod())
        {
            case 'GET':
                $urlParams = $this->dispatcher->getParams();
                $query = $this->request->getQuery();
                unset($query['_url']);
                return array_merge($urlParams, $query);
            case 'POST':
            case 'DELETE':
            case 'PUT':
                $json = $this->request->getJsonRawBody();
                foreach ($json as $k => $v)
                {
                    $ret[$k] = $v;
                }
                return $ret;
            default:
                return false;
        }
    }

    /**
     * アクション実行後に実行される処理です。
     * @param Dispatcher $dispatcher
     */
    public function afterExecuteRoute(Dispatcher $dispatcher)
    {
        $this->view->disable();

        $this->response->setContentType('application/json', 'UTF-8');
        $data = $dispatcher->getReturnedValue();
        $this->response->setJsonContent($data);
        echo $this->response->send();
    }
}