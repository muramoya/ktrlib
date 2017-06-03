<?php
/**
 * KTRLib\UnitTestCase
 * ユニットテストの基底クラスです。
 *
 * テストケースクラスはこのクラスをextendしてください。
 *
 * <code>
 * <?php
 * namespace Sample\UnitTest;
 *
 * use KTR\DataBase\Seeder;
 * use KTRLib\UnitTestCase;
 *
 * class SampleTest extends UnitTestCase
 * {
 *    public static function setUpBeforeClass()
 *    {
 *        //DBへのシード。引数にクラス名を指定するとdatabase/seeds以下の該当クラスのみ実行
 *        $seeder = new Seeder('SampleSeed,Sample2Seed');
 *        $seeder->run();
 *    }
 *
 *    public function testSample()
 *    {
 *        $this->assertTrue(true);
 *    }
 *
 * }
 * </code>
 *
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