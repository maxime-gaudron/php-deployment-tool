<?php

namespace QaSystem\CoreBundle\Service;

use Doctrine\ORM\EntityManager;
use QaSystem\CoreBundle\Workflow\Engine;
use QaSystem\CoreBundle\Entity\Job;

class DeploymentTool
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Engine
     */
    private $workflowEngine;

    /**
     * @param EntityManager $em
     * @param Engine $workflowEngine
     */
    public function __construct(EntityManager $em, Engine $workflowEngine)
    {
        $this->em = $em;
        $this->workflowEngine = $workflowEngine;
    }

    /**
     * @param Job $deployment
     *
     * @throws \RuntimeException
     */
    public function run(Job $deployment)
    {
        $deployment->setStatus(Job::STATUS_RUNNING);
        $this->em->persist($deployment);
        $this->em->flush();

        try {
            $returnValue = $this->workflowEngine->run($deployment);
            $status = $returnValue ? Job::STATUS_DONE : Job::STATUS_ERROR;
        } catch (\Exception $e) {
            $status = Job::STATUS_ERROR;
            $deployment->setOutput(
                sprintf('%s - %s - %s', get_class($e), $e->getCode(), $e->getMessage())
            );
        }

        $deployment->setStatus($status);
        $this->em->persist($deployment);
        $this->em->flush();
    }
}
