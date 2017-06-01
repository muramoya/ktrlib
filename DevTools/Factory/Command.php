<?php
/**
 * ユーザ定義kotoriコマンドファイル生成クラス
 * Date: 2017/05/28
 * @author takuya
 * @version: 1.0
 */

namespace KTRLib\DevTools\Factory;

use KTRLib\Config;
use Symfony\Component\Console\Command\Command as SyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends SyCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('make:command')
            ->setDescription('Make command file')
            ->setDefinition([
                new InputArgument('commandName', InputArgument::REQUIRED),
                new InputOption('class', 'c', InputOption::VALUE_REQUIRED, 'Class name here')
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $appConf = Config::factory('app.php');
        if(strlen($appConf->appNameSpace) < 1)
        {
            $output->writeln("<bg=red> Your application namespace hasn't been configured.\nPlease configure namespace at dotenv file.</bg=red>");
            return false;
        }

        $stub = file_get_contents(__DIR__ . '/stubs/command.stub');
        $stub = str_replace('DummyNameSpace', $appConf->appNameSpace , $stub);
        $stub = str_replace('DummyCommand',  $input->getArgument('commandName'), $stub);
        $stub = str_replace('DummyClass',  $input->getOption('class'), $stub);


        $fileName = $input->getOption('class') . '.php';
        $fileDir = APP_BASE_PATH . '/apps/commands';
        if(!is_dir($fileDir)) mkdir($fileDir, 0755);
        $path = realpath($fileDir) . '/' . $fileName;

        if (file_exists($path))
        {
            $output->writeln('<bg=red>' . $input->getOption('class') . ' is already exists.</bg=red>');
            return false;
        }

        file_put_contents($path, $stub);

        $fullNameSpace = $appConf->appNameSpace . '\\Apps\\Commands\\' . $input->getOption('class');

        $confFile = APP_BASE_PATH . '/conf/commands.php';
        $conf = file($confFile);
        $searchStrs = array_map(function($val) {
            return trim($val, PHP_EOL);
        }, $conf);
        //配列の最後([...]; or array(...);)の行を探す
        $key = array_search('];', $searchStrs);
        if($key === false) $key = array_search(');', $searchStrs);
        array_splice($conf, $key, 0, "    '" . $fullNameSpace . "',\n");
        file_put_contents($confFile, $conf);

        $output->writeln('<info>' . $input->getOption('class') . ' created successfully.</info>');
        return true;
    }
}