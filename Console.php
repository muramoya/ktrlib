<?php
/**
 * kotoriコマンドbootstrap
 * Date: 2017/06/01
 * @author takuya
 * @version: 1.0
 */

namespace KTRLib;

use Dotenv\Dotenv;
use KTRLib\Config;
use Phalcon\Loader;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;


class Console
{
    /**
     * @var InputInterface
     */
    private $input = null;

    public function __construct($command = null)
    {
        if (!is_null($command))$this->input = new ArrayInput(['command' => $command]);
    }

    public function run()
    {
        $cli = new Application('Kotori DevTools', '1.1.0');

        $commands = Config::factory(__DIR__ . '/DevTools/commands.php')->toArray();
        $userCom = Config::factory('commands.php');
        if($userCom !== false && !empty($userCom->toArray())) $commands = array_merge($commands, $userCom->toArray());

        $classes = [];

        foreach ($commands as $command)
        {
            $classes[] = new $command;
        }

        $cli->addCommands($classes);
        $cli->run($this->input);
    }

    public function setArgument($key, $val)
    {
        $this->input->setArgument($key, $val);
    }

    public function setOption($key, $val)
    {
        $this->input->setOption($key, $val);
    }
}