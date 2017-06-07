<?php

namespace KTRLib;

use Phalcon\Paginator\Adapter\Model as Paginator;

/**
 * モデルのデータ操作をするトレイトです。
 * モデルに対してCRUDを行うクラスでこのトレイトをuseしてください。
 *
 * <pre><code class="language-php">
 * <?php
 *
 * namespace Sample\Apps\Resources;
 *
 * use KTRLib\ResourcesTrait;
 *
 * class ResourcesCRUD
 * {
 *      use ResourcesTrait;
 *
 *      public function __construct()
 *     {
 *         $this->setModel('Resources');
 *     }
 * }
 * </code></pre>
 *
 * @author muramoya
 * @version: 1.0
 */

trait ResourcesTrait
{
    /**
     * ページネーションオブジェクト
     * @var Paginator
     */
    private $pagenate;
    /**
     * getPaginate
     * @var \stdClass
     */
    private $page;

    /**
     * @var \Phalcon\Mvc\Model
     */
    private $model;

    /**
     * 対象のモデルを設定します。
     * apps/models以下のモデルクラスが対象です。
     *
     * .envファイルにてAPP_NAMESPACEを設定してください。
     * 必ずこのメソッドをコールしてください。
     *
     * @param string $name
     */
    public function setModel($name)
    {
        $class = \env('APP_NAMESPACE').'\\Apps\\Models\\'.$name;
        $this->model = new $class;
    }

    /**
     * 条件を生成します。
     *
     * @param null $conditions
     * @param array $columns
     * @return array
     * @throws KtrRuntimeException
     */
    public function getConditions($conditions = null, $columns = array())
    {
        //$conditionsがnullの場合は全件、 intの場合はid(PK)
        if (is_null($conditions))
        {
            $conditions = [];
        }
        elseif (!is_array($conditions) && ctype_digit(strval($conditions)))
        {
            $conditions = [
                'conditions' => 'id = :id:',
                'bind' => ['id' => $conditions]
            ];
        }

        if (!is_array($conditions)) throw new KtrRuntimeException('Invalid conditions set');
        if (!empty($columns) && !is_array($columns)) throw new KtrRuntimeException('Invalid select columns set');

        if (!empty($columns) && is_array($columns)) $conditions['columns'] = $columns;

        return $conditions;
    }

    /**
     * リソースを複数件取得します。
     *
     * @param null $conditions
     * @param array $columns
     * @param bool $withDeleted
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getResources($conditions = null, $columns = array(), $withDeleted = false)
    {
        $cond = $this->getConditions($conditions, $columns);

        if (in_array('KTRLib\\ModelSoftDeleteTrait', class_uses($this->model)))
        {
            $res = $this->model::find($cond, $withDeleted);
        }
        else
        {
            $res = $this->model::find($cond);
        }

        return $res;
    }

    /**
     * リソースを1件取得します。
     *
     * @param null $conditions
     * @param array $columns
     * @param bool $withDeleted
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getOneResource($conditions = null, $columns = array(), $withDeleted = false)
    {
        $cond = $this->getConditions($conditions, $columns);

        if (in_array('KTRLib\\ModelSoftDeleteTrait', class_uses($this->model)))
        {
            $res = $this->model::findFirst($cond, $withDeleted);
        }
        else
        {
            $res = $this->model::findFirst($cond);
        }

        return $res;
    }

    /**
     * ページングを実行します。
     * 指定がなければ最初(=1)のページ @param $page
     * @param $count
     * @param $list
     * @return $this
     */
    public function paging($list, $count, $page = 1)
    {
        $pagenator = new Paginator(
            [
                'data' => $list,
                'limit' => $count,
                'page' => $page
            ]
        );

        $this->pagenate = $pagenator;
        $this->page = $pagenator->getPaginate();
        return $this;
    }

    /**
     * ページング: 現在のページのデータ一覧を取得します。
     *
     * @return mixed
     * @throws KtrRuntimeException
     */
    public function getCurrentPage()
    {
        if (!$this->pagenate) throw new KtrRuntimeException('Undefined Pagenator Object');

        return $this->page->items;
    }

    /**
     * ページング: 現在のページ数を取得します。
     *
     * @return mixed
     * @throws KtrRuntimeException
     */
    public function getCurrentPageNum()
    {
        if (!$this->page) throw new KtrRuntimeException('Undefined Pagenator Object');

        return $this->page->current;
    }

    /**
     * ページング: 総ページ数を取得します。
     *
     * @return mixed
     * @throws KtrRuntimeException
     */
    public function getTotalPagesNum()
    {
        if (!$this->page) throw new KtrRuntimeException('Undefined Pagenator Object');

        return $this->page->total_pages;
    }

    /**
     * ページング: ページ数リストを取得します。
     *
     * @return mixed
     * @throws KtrRuntimeException
     */
    public function getTotalPagesNumList()
    {
        if (!$this->pagenate) throw new KtrRuntimeException('Undefined Pagenator Object');
        $total = $this->getTotalPagesNum();
        if ($total < 1) return [1];

        foreach (range(1, $total) as $num)
        {
            $ret[] = $num;
        }

        return $ret;
    }

    /**
     * 総件数を取得します。
     *
     * @return mixed
     * @throws KtrRuntimeException
     */
    public function getTotalItemsNum()
    {
        if (!$this->page) throw new KtrRuntimeException('Undefined Pagenator Object');

        return $this->page->total_items;
    }

    /**
     * Insert,UpdateのパラメータをSetします。
     * Updateの時は第2引数に対象のモデルインスタンスを渡してください。
     *
     * @param array $params
     * @param null $model
     * @return bool|null
     */
    public function setValues(array $params, $model = null)
    {
        if (is_null($model))
        {
            foreach ($params as $column => $value)
            {
                $this->model->$column = $value;
            }

            return true;
        }
        else
        {
            foreach ($params as $column => $value)
            {
                $model->$column = $value;
            }
            return $model;
        }
    }

    /**
     * Insert
     *
     * @param $params
     * @return bool
     * @throws KtrRuntimeException
     */
    public function createResource(array $params)
    {
        if (empty($params)) throw new KtrRuntimeException('No params set');
        $this->setValues($params);
        $res = $this->model->save();
        $this->errorHandle($res);
        return $res;
    }

    /**
     * Update
     *
     * @param $id
     * @param $params
     * @return mixed
     * @throws KtrRuntimeException
     */
    public function updateResource($id,  array $params)
    {
        if (strlen((string)$id) < 1 || empty($params)) throw new KtrRuntimeException('No conditions or params set');
        $resource = $this->getOneResource($id);
        $res = $resource->update($params);
        $this->errorHandle($res);
        return $res;
    }

    /**
     * Delete
     *
     * @param $id
     * @return mixed
     * @throws KtrRuntimeException
     */
    public function deleteResource($id)
    {
        if (strlen((string)$id) < 1) throw new KtrRuntimeException('No conditions set');

        $resource = $this->getOneResource($id);
        if (!$resource) return false;
        $res = $resource->delete();
        $this->errorHandle($res);
        return $res;
    }

    private function errorHandle($res)
    {
        if(!$res)
        {
            $logger = new Logger();
            foreach ($this->model->getMessages() as $message)
            {
                $logger->error($message->getMessage());
            }
            throw new KtrRuntimeException('Database manipulation error has occurred');
        }
    }
}