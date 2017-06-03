<?php
/**
 * KTRLib\ModelTimestampableTrait
 *
 * テーブルのカラムに対しinsert,update,deleteが実行された時にタイムスタンプを押すようにするトレイトです。
 *
 * このトレイトはモデルクラスに対してuseしてください。
 * また、このモデルに紐づくDBのテーブルにはcreated_at,updated_teカラムを設定してください。
 * マイグレーションファイルでtimestampableメソッドをコールすると対応したカラムが設定されるので便利です。
 *
 * <code>
 * <?php
 *
 * namespace Sample\Apps\Models;
 *
 * use KTRLib\ModelTimestampableTrait;
 * use Phalcon\Mvc\Model;
 *
 * class Resources extends Model
 * {
 *     use ModelTimestampableTrait;
 *
 *     public $id;
 *     public $name;
 *     public $key;
 *
 *     public function initialize()
 *     {
 *        $this->setSource('resources');
 *        $this->addTimestampableBehavior();
 *     }
 * }
 * </code>
 *
 * @author muramoya
 * @version: 1.0
 */

namespace KTRLib;

use Phalcon\Mvc\Model\Behavior\Timestampable;

trait ModelTimestampableTrait
{
    public $created_at;
    public $updated_at;

    /**
     * ビヘイビアを設定します(タイムスタンプ)
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