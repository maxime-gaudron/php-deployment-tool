<?php

namespace QaSystem\CoreBundle\Service;

use Monolog\Logger;
use Doctrine\ORM\EntityManager;
use QaSystem\CoreBundle\Entity\Project;
use QaSystem\CoreBundle\Workflow\Engine;
use QaSystem\CoreBundle\Entity\Deployment;

class DeploymentTool
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     * @param Logger $logger
     */
    public function __construct(EntityManager $em, Logger $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function checkout(Project $project, $branch)
    {
        $project->getRepository()->checkout($branch);

        $this->logger->info("Checkout branch $branch of project " . $project->getName());
    }

    public function pull(Project $project)
    {
        $project->getRepository()->pull();

        $this->logger->info("Pulled project " . $project->getName());
    }

    public function deploy(Deployment $deployment)
    {
        $recipe = json_decode($deployment->getRecipe()->getWorkflow(), true);

        $workflowEngine = new Engine($this->logger);
        $workflowEngine->setEnvironment($deployment->getProject()->getUri());
        $workflowEngine->setRecipe($recipe);

        $this->logger->info(
            "Deploying project " . $deployment->getProject()->getName()
            . " using recipe " . $deployment->getRecipe()->getName()
        );

        $deployment->setStartDate(new \DateTime());
        $deployment->setStatus(Deployment::STATUS_DEPLOYING);
        $this->em->persist($deployment);
        $this->em->flush();

        $returnValue = $workflowEngine->run();
        $status = $returnValue ? Deployment::STATUS_DEPLOYED : Deployment::STATUS_ERROR;

        $this->logger->info(
            "End deployment project " . $deployment->getProject()->getName()
            . " using recipe " . $deployment->getRecipe()->getName()
            . " status: $status"
        );

        $deployment->setEndDate(new \DateTime());
        $deployment->setOutput($workflowEngine->getOutput());
        $deployment->setStatus($status);
        $this->em->persist($deployment);
        $this->em->flush();
    }
}
