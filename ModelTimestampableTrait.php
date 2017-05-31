<?php
/**
 * trait Model created_at, updated_atに自動でタイムスタンプを設定
 * Date: 2016/08/25
 * @author takuya
 * @version: 1.0
 */

namespace KTR;

use Phalcon\Mvc\Model\Behavior\Timestampable;

trait ModelTimestampableTrait
{
    public $created_at;
    public $updated_at;

    /**
     * ビヘイビアを設定(タイムスタンプ)
     */
    protected function addTimestampableBehavior()
    {
        $params = [
            'beforeCreate' => [
                'field'  => ['created_at','updated_at'],
                'format' => 'Y-m-d H:i:s'
            ],
            'beforeUpdate' => [
                'field'  => 'updated_at',
                'format' => 'Y-m-d H:i:s'
            ],
        ];
        $this->addBehavior(new Timestampable($params));
    }
}