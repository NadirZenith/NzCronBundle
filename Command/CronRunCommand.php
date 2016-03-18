<?php
namespace Nz\CronBundle\Command;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Console\Input\ArgvInput;

use Nz\CronBundle\Entity\JobResult;

use Nz\CronBundle\Entity\Job;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Input\InputArgument;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CronRunCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("nz:cron:run")
             ->setDescription("Runs any currently schedule cron jobs")
             ->addArgument("job", InputArgument::OPTIONAL, "Run only this job (if enabled)");
    }
    
    protected function getEntityManager()
    {
        
        if (!$this->getContainer()->get("doctrine")->getManager()->isOpen()) {
            $this->getContainer()->get("doctrine")->resetManager();
        }
        
        return $this->getContainer()->get("doctrine")->getManager();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $em = $this->getEntityManager();
        $jobRepo = $em->getRepository('NzCronBundle:Job');
        
        $jobsToRun = array();
        if($jobName = $input->getArgument('job'))
        {
            try
            {
                $jobObj = $jobRepo->findOneByCommand($jobName);
                if($jobObj->getEnabled())
                {
                    $jobsToRun = array($jobObj);
                }
            }
            catch(\Exception $e)
            {
                $output->writeln("Couldn't find a job by the name of $jobName");
                return JobResult::FAILED;
            }
        }
        else
        {
            $jobsToRun = $jobRepo->findDueTasks();
        }
        
        $jobCount = count($jobsToRun);
        $output->writeln("Running $jobCount jobs:");
        
        foreach($jobsToRun as $job)
        {
            $this->runJob($job, $output);
        }
        
        $end = microtime(true);
        $duration = sprintf("%0.2f", $end-$start);
        $output->writeln("Cron run completed in $duration seconds");
    }
    
    protected function runJob(Job $job, OutputInterface $output)
    {
        $output->write("Running " . $job->getCommand() . ": ");
        
        try
        {
            $commandToRun = $this->getApplication()->get($job->getCommand());
        }
        catch(InvalidArgumentException $ex)
        {
            $output->writeln(" skipped (command no longer exists)");
            $this->recordJobResult( $job, 0, "Command no longer exists", JobResult::SKIPPED);

            // No need to reschedule non-existant commands
            return;
        }
        
        $emptyInput = new ArgvInput();
        $jobOutput = new MemoryWriter();
        
        $jobStart = microtime(true);
        try
        {
            $returnCode = $commandToRun->execute($emptyInput, $jobOutput);
            
        }
        catch(\Exception $ex)
        {
            $returnCode = JobResult::FAILED;
            $jobOutput->writeln("");
            $jobOutput->writeln("Job execution failed with exception " . get_class($ex) . ":");
            $jobOutput->writeln($ex->__toString());
        }
        $jobEnd = microtime(true);
        
        // Clamp the result to accepted values
        if($returnCode < JobResult::RESULT_MIN || $returnCode > JobResult::RESULT_MAX)
        {
            $returnCode = JobResult::FAILED;
        }
        
        // Output the result
        $statusStr = "unknown";
        if($returnCode == JobResult::SKIPPED)
        {
            $statusStr = "skipped";
        }
        elseif($returnCode == JobResult::SUCCEEDED)
        {
            $statusStr = "succeeded";
        }
        elseif($returnCode == JobResult::FAILED)
        {
            $statusStr = "failed";
        }
        
        $durationStr = sprintf("%0.2f", $jobEnd-$jobStart);
        $output->writeln("$statusStr in $durationStr seconds");
        
        // And update the job with it's next scheduled time
        $newTime = new \DateTime();
        $newTime = $newTime->add(new \DateInterval($job->getInterval()));
        $job->setNextRun($newTime);
        
        // Record the result
        $this->recordJobResult( $job, $jobEnd-$jobStart, $jobOutput->getOutput(), $returnCode);
    }
    
    protected function recordJobResult( Job $job, $timeTaken, $output, $resultCode)
    {
        $em = $this->getEntityManager();
        //merge in case its detached due to errors
        $job = $em->merge($job);
        
        // Create a new JobResult
        $result = new JobResult();
        $result->setJob($job);
        $result->setRunTime($timeTaken);
        $result->setOutput($output);
        $result->setResult($resultCode);
        
        // Then update associations and persist it
        $job->setMostRecentRun($result);
        
        $em->persist($result);
        $em->flush();
    }
}
