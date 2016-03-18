<?php

namespace Nz\CronBundle\Entity;

use Doctrine\ORM\EntityRepository;

class JobRepository extends EntityRepository
{

    public function getKnownJobs()
    {
        $data = $this->getEntityManager()
            ->createQuery("SELECT job.command FROM NzCronBundle:Job job")
            ->getScalarResult();
        $toRet = array();
        foreach ($data as $datum) {
            $toRet[] = $datum['command'];
        }
        return $toRet;
    }

    public function findDueTasks()
    {
        return $this->getEntityManager()
                ->createQuery("SELECT job FROM NzCronBundle:Job job
                                              WHERE job.nextRun <= :curTime
                                              AND job.enabled = 1")
                ->setParameter('curTime', new \DateTime())
                ->getResult();
    }
}
