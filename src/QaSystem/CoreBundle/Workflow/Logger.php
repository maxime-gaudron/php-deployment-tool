<?php

namespace QaSystem\CoreBundle\Workflow;

use Doctrine\ORM\EntityManager;
use QaSystem\CoreBundle\Entity\Job;

/**
 * Class Logger
 * @package QaSystem\CoreBundle\Workflow
 */
class Logger
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string     $message
     * @param Job $deployment
     */
    public function info($message, Job $deployment)
    {
        $deployment->setOutput($deployment->getOutput() . $message );
        $this->entityManager->persist($deployment);
        $this->entityManager->flush();
    }

    /**
     * @param string     $message
     * @param Job $deployment
     */
    public function error($message, Job $deployment)
    {
        $deployment->setOutput($deployment->getOutput() . $message);
        $this->entityManager->persist($deployment);
        $this->entityManager->flush();
    }
}
