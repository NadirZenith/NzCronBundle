<?php

namespace Nz\CronBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="nz__cron_job_result")
 * @ORM\Entity(repositoryClass="Nz\CronBundle\Entity\JobResultRepository")
 */
class JobResult
{

    const RESULT_MIN = 0;
    const SUCCEEDED = 0;
    const FAILED = 1;
    const SKIPPED = 2;
    const RESULT_MAX = 2;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer $id
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime $runAt
     */
    protected $runAt;

    /**
     * @ORM\Column(type="float")
     * @var float $runTime
     */
    protected $runTime;

    /**
     * @ORM\Column(type="integer")
     * @var integer $result
     */
    protected $result;

    /**
     * @ORM\Column(type="text")
     * @var string $output
     */
    protected $output;

    /**
     * @ORM\ManyToOne(targetEntity="Job", inversedBy="results")
     * @var Job
     */
    protected $job;

    public function __construct()
    {
        $this->runAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set runAt
     *
     * @param datetime $runAt
     */
    public function setRunAt($runAt)
    {
        $this->runAt = $runAt;
    }

    /**
     * Get runAt
     *
     * @return datetime 
     */
    public function getRunAt()
    {
        return $this->runAt;
    }

    /**
     * Set runTime
     *
     * @param float $runTime
     */
    public function setRunTime($runTime)
    {
        $this->runTime = $runTime;
    }

    /**
     * Get runTime
     *
     * @return float 
     */
    public function getRunTime()
    {
        return $this->runTime;
    }

    /**
     * Set result
     *
     * @param integer $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * Get result
     *
     * @return integer 
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set output
     *
     * @param string $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * Get output
     *
     * @return string 
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Set job
     *
     * @param Nz\CronBundle\Entity\Job $job
     */
    public function setJob(\Nz\CronBundle\Entity\Job $job)
    {
        $this->job = $job;
    }

    /**
     * Get job
     *
     * @return Nz\CronBundle\Entity\Job 
     */
    public function getJob()
    {
        return $this->job;
    }
    
    public function __toString()
    {
        return $this->output;
    }
}
