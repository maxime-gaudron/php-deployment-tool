<?php

namespace QaSystem\CoreBundle\Workflow;

use Doctrine\ORM\EntityManager;
use QaSystem\CoreBundle\Entity\Deployment;

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
     * @var Deployment
     */
    private $deploymentEntity;

    /**
     * @param EntityManager $entityManager
     * @param Deployment $deploymentEntity
     */
    public function __construct(EntityManager $entityManager, Deployment $deploymentEntity)
    {
        $this->deploymentEntity = $deploymentEntity;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $message
     */
    public function info($message)
    {
        $output = $this->deploymentEntity->getOutput();
        $output .= '<span class="info-output">' . $message . "</span>";

        $this->deploymentEntity->setOutput($output);
        $this->entityManager->persist($this->deploymentEntity);
        $this->entityManager->flush();
    }

    /**
     * @param string $message
     */
    public function error($message)
    {
        $output = $this->deploymentEntity->getOutput();
        $output .= '<span class="error-output">' . $message . "</span>";

        $this->deploymentEntity->setOutput($output);
        $this->entityManager->persist($this->deploymentEntity);
        $this->entityManager->flush();
    }
}
