<?php
/**
 * コントローラーファイル生成クラス
 * Date: 2017/05/28
 * @author takuya
 * @version: 1.0
 */

namespace KTR\DevTools\Factory;

use KTR\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Controller extends Command
{
    protected function configure()
    {
        parent::configure();
        $this->setName('make:controller')
             ->setDescription('Make controller file')
             ->setDefinition([
                new InputArgument('name', InputArgument::REQUIRED),
             ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $appConf = Config::factory('app.php');
        $stub = file_get_contents(__DIR__ . '/stubs/controller.stub');
        $stub = str_replace('DummyNameSpace', $appConf->appNameSpace , $stub);
        $stub = str_replace('DummyClass',  $input->getArgument('name'), $stub);

        $fileName = $input->getArgument('name') . '.php';
        $path = realpath(__DIR__ . '/../../../apps/controllers') . '/' . $fileName;

        if (file_exists($path))
        {
            $output->writeln('<bg=red>' . $input->getArgument('name') . ' is already exists.</bg=red>');
            return false;
        }

        file_put_contents($path, $stub);

        $output->writeln('<info>' . $input->getArgument('name') . ' created successfully.</info>');
    }
}