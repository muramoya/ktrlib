<?php
/**
 * マイグレーションファイル抽象クラス
 * Date: 2017/05/30
 * @author takuya
 * @version: 1.0
 */

namespace KTR\DevTools\Migration;

use Phinx\Db\Table;
use Phinx\Migration\AbstractMigration as PhAbstMig;

abstract class AbstractMigration extends PhAbstMig
{
    private $args = [];

    /**
     * @var Table
     */
    public $table;

    public function tableName($name)
    {
        $table = $this->table($name, ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'integer', ['identity' => true]);
        $this->table = $table;
    }
    
    public function string($name)
    {
        $this->columnName($name);
        $this->columnType('string');
        return $this;
    }

    public function integer($name)
    {
        $this->columnName($name);
        $this->columnType('integer');
        return $this;
    }

    public function bigint($name)
    {
        $this->columnName($name);
        $this->columnType('biginteger');
        return $this;
    }

    public function binary($name)
    {
        $this->columnName($name);
        $this->columnType('binary');
        return $this;
    }

    public function boolean($name)
    {
        $this->columnName($name);
        $this->columnType('boolean');
        return $this;
    }

    public function date($name)
    {
        $this->columnName($name);
        $this->columnType('date');
        return $this;
    }

    public function datetime($name)
    {
        $this->columnName($name);
        $this->columnType('datetime');
        return $this;
    }

    public function decimal($name)
    {
        $this->columnName($name);
        $this->columnType('decimal');
        return $this;
    }

    public function float($name)
    {
        $this->columnName($name);
        $this->columnType('float');
        return $this;
    }

    public function text($name)
    {
        $this->columnName($name);
        $this->columnType('text');
        return $this;
    }

    public function time($name)
    {
        $this->columnName($name);
        $this->columnType('time');
        return $this;
    }

    public function timestamp($name)
    {
        $this->columnName($name);
        $this->columnType('timestamp');
        return $this;
    }

    public function uuid($name)
    {
        $this->columnName($name);
        $this->columnType('uuid');
        return $this;
    }

    public function blob($name)
    {
        $this->columnName($name);
        $this->columnType('blob');
        return $this;
    }

    public function json($name)
    {
        $this->columnName($name);
        $this->columnType('json');
        return $this;
    }

    public function default($val)
    {
        $this->columnOption(__FUNCTION__, $val);
        return $this;
    }

    public function increments()
    {
        $this->columnOption('identity', true);
        return $this;
    }

    public function unsigned()
    {
        $this->columnOption('signed', true);
        return $this;
    }

    public function size($val)
    {
        $this->columnOption('limit', $val);
        return $this;
    }

    public function nullable()
    {
        $this->columnOption('null', true);
        return $this;
    }

    public function after($name)
    {
        $this->columnOption('after', $name);
        return $this;
    }

    public function comment($val)
    {
        $this->columnOption('comment', $val);
        return $this;
    }

    //モデルトレイトのタイムスタンプカラムを追加
    public function timestampable()
    {
        $this->datetime('created_at')
             ->add();
        $this->datetime('updated_at')
            ->add();
    }

    //モデルトレイトの論理削除カラムを追加
    public function softdelete()
    {
        $this->datetime('deleted_at')
            ->nullable()
            ->add();
    }

    public function unique()
    {
        $name = $this->args['name'];
        $this->table->addIndex([$name], ['unique' => true, 'name' => 'idx_' . $this->table->getName() . '_' . $name]);
        return $this;
    }

    public function addUniqueIndex(array $columns)
    {
        $name = implode('_', $columns);
        $this->table->addIndex($columns, ['unique' => true, 'name' => 'idx_' . $this->table->getName() . '_' . $name]);
    }

    public function index()
    {
        $name = $this->args['name'];
        $this->table->addIndex([$name], ['unique' => false, 'name' => 'idx_' . $this->table->getName() . '_' . $name]);
        return $this;
    }

    public function addIndex(array $columns)
    {
        $name = implode('_', $columns);
        $this->table->addIndex($columns, ['unique' => false, 'name' => 'idx_' . $this->table->getName() . '_' . $name]);
    }

    public function addForeignKey($column, $referenceTable, $referenceColumn, $option = array())
    {
        $this->table->addForeignKey($column, $referenceTable, $referenceColumn, $option);

    }

    public function add()
    {
        $params = $this->args;
        if(!isset($params['option']['null'])) $params['option']['null'] = false;
        $this->table->addColumn($params['name'], $params['type'], $params['option']);
        $this->args = [];
    }

    public function create()
    {
        $this->table->create();
    }

    public function columnName($name)
    {
        $this->args['name'] = $name;
    }

    public function columnType($type)
    {
        $this->args['type'] = $type;
    }

    public function columnOption($key, $val)
    {
        $this->args['option'][$key] = $val;
    }
}