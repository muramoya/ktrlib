<?php
/**
 * UnitTest 基底クラス
 * Date: 2016/07/06
 * @author muramoya
 * @version: 1.0
 */

namespace KTRLib;

use Phalcon\DI;
use Phalcon\Test\FunctionalTestCase as PhalconTestCase;

abstract class UnitTestCase extends PhalconTestCase
{
    /**
     * @var \Phalcon\Config
     */
    protected $_config;

    /**
     * @var bool
     */
    private $_loaded = false;

    public function setUp()
    {
        $di = Di::getDefault();
        $this->setDI($di);
        $this->_loaded = true;
    }

    public function tearDown()
    {
    }
}