<?php

namespace Nz\CronBundle\Entity;

use Doctrine\ORM\EntityRepository;

class JobResultRepository extends EntityRepository
{

    public function deleteOldLogs(Job $job = null)
    {
        // Unfortunately, because we can't use DELETE k WHERE k.id > (SELECT MAX(k2.id) FROM k2)
        // we have to select the max IDs first
        $data = $this->getEntityManager()
            ->createQuery("SELECT job.id, MAX(result.id) FROM NzCronBundle:Job job
                                                                  JOIN job.results result
                                                                  WHERE result.result = :code
                                                                  GROUP BY result.job")
            ->setParameter('code', JobResult::SUCCEEDED)
            ->getResult();

        foreach ($data as $datum) {
            $jobId = $datum['id'];
            $minId = $datum[1];

            if (!$job || $job->getId() == $jobId) {
                $this->getEntityManager()->createQuery("DELETE NzCronBundle:JobResult result
                                                        WHERE result.id < :minId
                                                        AND result.job = :jobId")
                    ->setParameter('minId', $minId)
                    ->setParameter('jobId', $jobId)
                    ->getResult();
            }
        }
    }
}
