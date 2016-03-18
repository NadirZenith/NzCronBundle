<?php

namespace Nz\CronBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Nz\CronBundle\Crontab\Manager;

class CronRemoveCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName("nz:cron:remove")
            ->setDescription("Remove symfony command in crontab");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //[Minute] [Hour] [Day] [Month] [Day of week (0 =sunday to 6 =saturday)] [Command]
        $format = '* * * * * %s %s %s'; //php_path console_path sym_command
        $php_bindir = rtrim(PHP_BINDIR, '/') . '/php';
        $console_path = rtrim($this->getContainer()->getParameter('kernel.root_dir'), '/') . '/console';
        $symfony_command = 'nz:cron:run';
        $command = sprintf($format, $php_bindir, $console_path, $symfony_command);

        $manager = new Manager();
        $return = $manager->remove_cronjob($command);

        $output->writeln(sprintf('Removed command: %s', $command));
    }
}
