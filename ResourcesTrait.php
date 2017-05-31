<?php
/**
 * データ一覧取得Trait
 * Date: 2016/08/06
 * @author takuya
 * @version: 1.0
 */

namespace KTR;

use Phalcon\Paginator\Adapter\Model as Paginator;

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

    public function setModel($name)
    {
        $class = \env('APP_NAMESPACE').'\\Models\\'.$name;
        $this->model = new $class;
    }

    /**
     * 条件を生成する
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
     * リソースを複数件取得する
     * @param null $conditions
     * @param array $columns
     * @param bool $withDeleted
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getResources($conditions = null, $columns = array(), $withDeleted = false)
    {
        $cond = $this->getConditions($conditions, $columns);

        if (in_array('KTR\\ModelSoftDeleteTrait', class_uses($this->model)))
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
     * リソースを1件取得する
     * @param null $conditions
     * @param array $columns
     * @param bool $withDeleted
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getOneResource($conditions = null, $columns = array(), $withDeleted = false)
    {
        $cond = $this->getConditions($conditions, $columns);

        if (in_array('KTR\\ModelSoftDeleteTrait', class_uses($this->model)))
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
     * ページングを実行する
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
     * 現在の一覧を取得
     * @return mixed
     * @throws KtrRuntimeException
     */
    public function getCurrentPage()
    {
        if (!$this->pagenate) throw new KtrRuntimeException('Undefined Pagenator Object');

        return $this->page->items;
    }

    /**
     * 現在のページを取得
     * @return mixed
     * @throws KtrRuntimeException
     */
    public function getCurrentPageNum()
    {
        if (!$this->page) throw new KtrRuntimeException('Undefined Pagenator Object');

        return $this->page->current;
    }

    /**
     * 総ページ数を取得
     * @return mixed
     * @throws KtrRuntimeException
     */
    public function getTotalPagesNum()
    {
        if (!$this->page) throw new KtrRuntimeException('Undefined Pagenator Object');

        return $this->page->total_pages;
    }

    /**
     * ページ数リストを取得
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
     * 総件数を取得
     * @return mixed
     * @throws KtrRuntimeException
     */
    public function getTotalItemsNum()
    {
        if (!$this->page) throw new KtrRuntimeException('Undefined Pagenator Object');

        return $this->page->total_items;
    }

    /**
     * Insert,UpdateのパラメータをSet
     * Updateの時は第2引数に対象のモデルインスタンスを渡す
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
     * @param $params
     * @return bool
     * @throws KtrRuntimeException
     */
    public function createResource(array $params)
    {
        if (empty($params)) throw new KtrRuntimeException('No params set');
        $this->setValues($params);
        return $this->model->save();
    }

    /**
     * Update
     * @param $id
     * @param $params
     * @return mixed
     * @throws KtrRuntimeException
     */
    public function updateResource($id,  array $params)
    {
        if (strlen((string)$id) < 1 || empty($params)) throw new KtrRuntimeException('No conditions or params set');
        $resource = $this->getOneResource($id);
        return $resource->update($params);
    }

    /**
     * Delete
     * @param $id
     * @return mixed
     * @throws KtrRuntimeException
     */
    public function deleteResource($id)
    {
        if (strlen((string)$id) < 1) throw new KtrRuntimeException('No conditions set');

        $resource = $this->getOneResource($id);
        $ret = $resource ? $resource->delete() : false;
        return $ret;
    }
}