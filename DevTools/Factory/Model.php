<?php
/**
 * モデルファイル生成クラス
 * Date: 2017/05/28
 * @author takuya
 * @version: 1.0
 */

namespace KTR\DevTools\Factory;

use KTR\Config;
use KTR\KtrRuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Model extends Command
{
    private const VALID_EXTENDS = ['softdelete', 'timestamp', 'both'];

    protected function configure()
    {
        parent::configure();
        $this->setName('make:model')
             ->setDescription('Make model file')
             ->setDefinition([
                 new InputArgument('table', InputArgument::REQUIRED),
                 new InputOption('className', '', InputOption::VALUE_OPTIONAL, 'set classname'),
                 new InputOption('extends', '', InputOption::VALUE_OPTIONAL, 'use softdelete, timestamp or both')
             ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $appConf = Config::factory('app.php');
        $extends = strlen($input->getOption('extends')) > 0 ? $input->getOption('extends') : 'default';
        $format = $this->formatExtendsOption($extends);
        if (!$format['res'])
        {
            $output->writeln('<bg=red>' . $format['msg'] . '</bg=red>');
            return false;
        }
        $extends = $format['option'];
        $stub = file_get_contents(__DIR__ . '/stubs/model_' . $extends . '.stub');
        $stub = str_replace('DummyNameSpace', $appConf->appNameSpace , $stub);
        $class = strlen($input->getOption('className')) > 0 ? $input->getOption('className') : camelize($input->getArgument('table'));
        $stub = str_replace('DummyClass', $class, $stub);
        $stub = str_replace('DummyTable', $input->getArgument('table'), $stub);


        $fileName = $class . '.php';
        $path = realpath(__DIR__ . '/../../../apps/models') . '/' . $fileName;

        if (file_exists($path))
        {
            $output->writeln('<bg=red>' . $class . ' is already exists.</bg=red>');
            return false;
        }

        file_put_contents($path, $stub);

        $output->writeln('<info>' . $class . ' created successfully.</info>');
        return true;
    }

    private function formatExtendsOption($option)
    {
        if (strpos($option, ',') !== false)
        {
            $arr = explode(',', $option);

            if ((in_array('softdelete', $arr) || in_array('timestamp', $arr))
                && in_array('both', $arr))
            {
                return ['res' => false, 'msg' => "Don't set extends option 'softdelete' or 'timestamp' and 'both' together." ];
            }
            elseif (!empty(array_diff($arr, self::VALID_EXTENDS)))
            {
                return ['res' => false, 'msg' => "Invalid extends option given." ];
            }

            if (in_array('softdelete', $arr) && in_array('timestamp', $arr))
            {
                return ['res' => true, 'option' => 'both'];
            }
            else
            {
                return ['res' => true, 'option' => trim($option, ',')];
            }

        }
        else
        {
            if (!in_array($option, self::VALID_EXTENDS))
            {
                return ['res' => false, 'msg' => "Invalid extends option given." ];
            }

            return ['res' => true, 'option' => $option];

        }
    }
}