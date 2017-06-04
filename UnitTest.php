<?php
namespace KTRLib;

use Phalcon\Loader;
use Phalcon\Di\FactoryDefault;
use Phalcon\Di;

/**
 * ユニットテストのBootstrapクラスです。
 *
 * @author muramoya
 * @version: 1.0
 */

class UnitTest
{
    public function run()
    {
        $di = new FactoryDefault();

        /*
         * services
         ************************/
        $serviceConf = \KTRLib\Config::factory('services.php');
        if($serviceConf->count() > 0) {
            foreach ($serviceConf as $name => $class) {
                $di->set($name, $class);
            }
        }

        //クロージャでの登録
        require_once APP_BASE_PATH . '/conf/services_from_closure.php';

        /*
         * dotenv
         ************************/
        $di->get('env');

        /*
         * autoload
         ************************/
        /**
         * @var Phalcon\Config
         */
        $loadConf = \KTRLib\Config::factory('loader.php');
        if (!empty($loadConf))
        {
            $loader = new Loader();
            $loader->registerNamespaces($loadConf->toArray())->register();
        }

        Di::setDefault($di);
    }
}