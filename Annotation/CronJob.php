<?php
namespace Nz\CronBundle\Annotation;

/**
 * @Annotation()
 * @Target("CLASS")
 */
use Doctrine\Common\Annotations\Annotation;

class CronJob extends Annotation
{
    public $value;
}
