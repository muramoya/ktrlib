<?php
/**
 * trait Model delete時に論理削除をする
 * Date: 2016/08/25
 * @author takuya
 * @version: 1.0
 */

namespace KTR;

use Phalcon\Mvc\Model\Behavior\SoftDelete;

trait ModelSoftDeleteTrait
{
    public $deleted_at;

    /**
     * ビヘイビアを設定(論理削除)
     */
    protected function addSoftDeleteBehavior() {
        $params = [
            'field' => 'deleted_at',
            'value' => date('Y-m-d H:i:s')
        ];
        $this->addBehavior(new SoftDelete($params));
    }

    /**
     * Modelのfindを指定がなければdeleted=0で検索するようオーバーライド
     * @param null $parameters
     * @param bool $withDeleted
     * @return Model\ResultsetInterface
     */
    public static function find($parameters = null, $withDeleted = false) {
        $isNotDeleted = 'deleted_at IS NULL';
        if (!$withDeleted) {
            $parameters = self::appendParams($parameters, $isNotDeleted);
        }
        return parent::find($parameters);
    }

    /**
     * ModelのfindFirstを指定がなければdeleted=0で検索するようオーバーライド
     * @param null $parameters
     * @param bool $withDeleted
     * @return static
     */
    public static function findFirst($parameters = null, $withDeleted = false) {
        $isNotDeleted = 'deleted_at IS NULL';
        if (!$withDeleted) {
            $parameters = self::appendParams($parameters, $isNotDeleted);
        }
        return parent::findFirst($parameters);
    }

    /**
     * 渡されたパラメータにwhere文を追加する
     * @param $params
     * @param $append
     * @return array|string
     * @throws KtrRuntimeException
     */
    private static function appendParams($params, $append) {
        if ($params === null) {
            $params = $append;
        }
        elseif (is_array($params)) {
            if (isset($params[0])) {
                $params[0] .= ' AND ' . $append;
            } elseif (isset($params['conditions'])) {
                $params['conditions'] .= ' AND ' . $append;
            } elseif (empty($params)) {
                $params['conditions'] = $append;
            } else {
                throw new KtrRuntimeException('Undefined Params received');
            }

        } else {
            $params .= ' AND ' . $append;
        }
        return $params;
    }

}