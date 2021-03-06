<?php
namespace Nz\CronBundle\Command;
use Nz\CronBundle\Entity\JobResult;

use Nz\CronBundle\Entity\Job;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Input\InputArgument;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CronStatusCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("nz:cron:status")
             ->setDescription("Displays the current status of cron jobs")
             ->addArgument("job", InputArgument::OPTIONAL, "Show information for only this job");
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $output->writeln("Cron job statuses:");
        
        $cronJobs = array();
        if($jobName = $input->getArgument('job'))
        {
            try
            {
                $cronJobs = array($jobRepo->findOneByCommand($jobName));
            }
            catch(\Exception $e)
            {
                $output->writeln("Couldn't find a job by the name of $jobName");
                return JobResult::FAILED;
            }
        }
        else
        {
            $cronJobs = $em->getRepository('NzCronBundle:Job')->findAll();
        }
        
        foreach($cronJobs as $cronJob)
        {
            $output->write(" - " . $cronJob->getCommand());
            if(!$cronJob->getEnabled())
            {
                $output->write(" (disabled)");
            }
            $output->writeln("");
            $output->writeln("   Description: " . $cronJob->getDescription());
            if(!$cronJob->getEnabled())
            {
                $output->writeln("   Not scheduled");
            }
            else
            {
                $output->write("   Scheduled for: ");
                $now = new \DateTime();
                if($cronJob->getNextRun() <= $now)
                {
                    $output->writeln("Next run");
                }
                else
                {
                    $output->writeln(strftime("%c", $cronJob->getNextRun()->getTimestamp()));
                }
            }
            if($cronJob->getMostRecentRun())
            {
                $status = "Unknown";
                switch($cronJob->getMostRecentRun()->getResult())
                {
                    case JobResult::SUCCEEDED:
                        $status = "Successful";
                        break;
                    case JobResult::SKIPPED:
                        $status = "Skipped";
                        break;
                    case JobResult::FAILED:
                        $status = "Failed";
                        break;
                }
                $output->writeln("   Last run was: $status");
            }
            else
            {
                $output->writeln("   This job has not yet been run");
            }
            $output->writeln("");
        }
    }
}
