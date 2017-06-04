<?php
namespace KTRLib\DevTools\Db;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * kotoriコマンド
 * データベースシーディングを実行します。
 *
 * @author muramoya
 * @version: 1.1.0
 */
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
        $cmd = APP_BASE_PATH . '/vendor/bin/phinx';
        $args['configuration'] = __DIR__ . '/../Migration/phinx.php';
        $opt = '';
        if(strlen($input->getOption('seed')) > 0)
        {
            $targets = explode(',', $input->getOption('seed'));
            if(count($targets))
            {
                foreach ($targets as $target)
                {
                    $opt .= '-s ' . $target . ' ';
                }
            }
            else
            {
                $opt .= '-s' . $input->getOption('seed');
            }
        }
        $exec = sprintf($exec , $cmd, 'seed:run', $args['configuration'], 'dev', $opt);
        putenv('PH_APP_BASE_PATH=' . APP_BASE_PATH);
        exec($exec, $out, $ret);
        putenv('PH_APP_BASE_PATH=');

        $msgType = $ret === 0 ? '<info>%s</info>' : '<bg=red>%s</bg=red>';
        $output->writeln(sprintf($msgType, implode("\n", $out)));

        return $ret === 0 ? true : false;
    }
}