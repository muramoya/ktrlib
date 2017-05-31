<?php
/**
 * マイグレーションファイル生成クラス
 * phinxをそのまま使っています
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

class Seeder extends Command
{
    protected function configure()
    {
        parent::configure();
        $this->setName('make:seeder')
             ->setDescription('Make seeder file')
             ->setDefinition([
                 new InputArgument('name', InputArgument::REQUIRED),
                 new InputOption('configuration', 'c', InputOption::VALUE_NONE)
             ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exec = '%s %s --configuration=%s %s 2>&1';
        $cmd = realpath(__DIR__ . '/../../../vendor') . '/bin/phinx';
        $args['configuration'] = __DIR__ . '/../Migration/phinx.php';
        $args['name'] = $input->getArgument('name');
        $exec = sprintf($exec , $cmd, 'seed:create', $args['configuration'], $args['name']);
        exec($exec, $out, $ret);

        $msgType = $ret === 0 ? '<info>%s</info>' : '<bg=red>%s</bg=red>';
        $output->writeln(sprintf($msgType, implode("\n", $out)));

        return $ret === 0 ? true : false;
    }
}