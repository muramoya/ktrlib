<?php
/**
 * マイグレーション実行クラス
 * phinxをそのまま使っています
 * Date: 2017/05/30
 * @author takuya
 * @version: 1.0
 */

namespace KTR\DevTools\Db;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Seed extends Command
{
    protected function configure()
    {
        parent::configure();
        $this->setName('db:seed')
            ->setDescription('Seed Data')
            ->setDefinition([
                new InputOption('seed', 's', InputOption::VALUE_OPTIONAL),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exec = '%s %s --configuration=%s -e %s %s 2>&1';
        $cmd = realpath(__DIR__ . '/../../../vendor') . '/bin/phinx';
        $args['configuration'] = __DIR__ . '/../Migration/phinx.php';
        $opt = '';
        if(strlen($input->getOption('seed')) > 0) $opt .= '-s' . $input->getOption('seed');

        $exec = sprintf($exec , $cmd, 'seed:run', $args['configuration'], 'dev', $opt);
        exec($exec, $out, $ret);

        $msgType = $ret === 0 ? '<info>%s</info>' : '<bg=red>%s</bg=red>';
        $output->writeln(sprintf($msgType, implode("\n", $out)));

        return $ret === 0 ? true : false;
    }
}