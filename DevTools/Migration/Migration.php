<?php
/**
 * マイグレーション実行クラス
 * phinxをそのまま使っています
 * Date: 2017/05/30
 * @author takuya
 * @version: 1.0
 */

namespace KTR\DevTools\Migration;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Migration extends Command
{
    protected function configure()
    {
        parent::configure();
        $this->setName('migration')
            ->setDescription('Migration')
            ->setDefinition([
                new InputArgument('manipulation', InputArgument::OPTIONAL, '', 'migrate'),
                new InputOption('target', 't', InputOption::VALUE_REQUIRED),
                new InputOption('date', 'd', InputOption::VALUE_REQUIRED)
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exec = '%s %s --configuration=%s -e %s %s 2>&1';
        $cmd = realpath(__DIR__ . '/../../../vendor') . '/bin/phinx';
        $args['configuration'] = __DIR__ . '/../Migration/phinx.php';
        $opt = '';
        if(strlen($input->getOption('target')) > 0)
        {
            $opt .= '-t' . $input->getOption('target');
        }
        elseif ($input->getArgument('manipulation') == 'rollback' && strlen($input->getOption('target')) == 0)
        {
            $opt .= '-t 0';
        }
        if(strlen($input->getOption('date') > 0)) $opt .= '-d' . $input->getOption('date');

        $exec = sprintf($exec , $cmd, $input->getArgument('manipulation'), $args['configuration'], 'dev', $opt);
        exec($exec, $out, $ret);

        $msgType = $ret === 0 ? '<info>%s</info>' : '<bg=red>%s</bg=red>';
        $output->writeln(sprintf($msgType, implode("\n", $out)));

        return $ret === 0 ? true : false;
    }
}