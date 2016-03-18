<?php

namespace Nz\CronBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CronEnableJobCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName("nz:cron:enable-job")
            ->setDescription("Enables a cron job")
            ->addArgument("job", InputArgument::REQUIRED, "Name of the job to enable");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobName = $input->getArgument('job');
        $em = $this->getContainer()->get("doctrine.orm.entity_manager");
        $jobRepo = $em->getRepository('NzCronBundle:Job');

        $job = $jobRepo->findOneByCommand($jobName);
        if (!$job) {
            $output->writeln("Couldn't find a job by the name of " . $jobName);
            return JobResult::FAILED;
        }

        $job->setEnabled(true);
        $em->flush();

        $output->writeln("Enabled cron job by the name of " . $jobName);
    }
}
