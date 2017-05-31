<?php
/**
 * マイグレーションファイル生成クラス
 * Date: 2017/05/28
 * @author takuya
 * @version: 1.0
 */

namespace KTR\DevTools\Factory;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Migrate extends Command
{
    protected function configure()
    {
        parent::configure();
        $this->setName('make:migrate')
             ->setDescription('Make migration file')
             ->setDefinition([
                new InputArgument('name', InputArgument::REQUIRED),
                new InputOption('create', 'create', InputOption::VALUE_OPTIONAL, 'table name')
             ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if(! preg_match('/^([A-Z][a-z0-9]+)+$/', $input->getArgument('name')))
        {
            throw new \InvalidArgumentException(sprintf(
                'The migration class name "%s" is invalid. Please use CamelCase format.',
                $input->getArgument('name')
            ));
        }
        $type = strlen($input->getOption('create')) > 0 ? 'create' : 'update';
        $stub = file_get_contents(__DIR__ . '/stubs/' . $type . '_table.stub');
        $stub = str_replace('DummyClass',  $input->getArgument('name'), $stub);
        if ($type = 'create') $stub = str_replace('DummyTable', $input->getOption('create'), $stub);

        $files = scandir(__DIR__ . '/../../../database/migrations');
        foreach ($files as $file)
        {
            if ($file == '.' || $file == '..') continue;
            if (strpos($file, underscore($input->getArgument('name'))) !== false)
            {
                $output->writeln('<bg=red>' . $input->getArgument('name') . ' class is already exists.</bg=red>');
                return false;
            }
        }
        $fileName = date('YmdHis') . '_' . underscore($input->getArgument('name')) . '.php';
        $path = realpath(__DIR__ . '/../../../database/migrations') . '/' . $fileName;
        file_put_contents($path, $stub);

        $output->writeln('<info>' . $path . ' created successfully.</info>');
    }
}