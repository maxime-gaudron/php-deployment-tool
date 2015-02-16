<?php

namespace QaSystem\CoreBundle\Service;

use Doctrine\ORM\EntityManager;
use QaSystem\CoreBundle\Workflow\Engine;
use QaSystem\CoreBundle\Entity\Deployment;

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
     * @param Deployment $deployment
     *
     * @throws \RuntimeException
     */
    public function deploy(Deployment $deployment)
    {
        $deployment->setStatus(Deployment::STATUS_DEPLOYING);
        $this->em->persist($deployment);
        $this->em->flush();

        try {
            $returnValue = $this->workflowEngine->run($deployment);
            $status = $returnValue ? Deployment::STATUS_DEPLOYED : Deployment::STATUS_ERROR;
        } catch (\Exception $e) {
            $status = Deployment::STATUS_ERROR;
            $deployment->setOutput(
                sprintf('%s - %s - %s', get_class($e), $e->getCode(), $e->getMessage())
            );
        }

        $deployment->setStatus($status);
        $this->em->persist($deployment);
        $this->em->flush();
    }
}
