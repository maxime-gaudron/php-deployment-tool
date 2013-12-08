<?php

namespace QaSystem\CoreBundle\Consumer;

use Monolog\Logger;
use Doctrine\ORM\EntityManager;
use PhpAmqpLib\Message\AMQPMessage;
use QaSystem\CoreBundle\Workflow\Engine;
use QaSystem\CoreBundle\Entity\Deployment;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;

class ProjectDeployConsumer implements ConsumerInterface
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
     * @var Engine
     */
    protected $workflowEngine;

    /**
     * @param EntityManager $em
     * @param Logger $logger
     * @param Engine $workflowEngine
     */
    function __construct(EntityManager $em, Engine $workflowEngine, Logger $logger)
    {
        $this->em = $em;
        $this->workflowEngine = $workflowEngine;
        $this->logger = $logger;
    }

    /**
     * @param AMQPMessage $msg
     * @return bool
     */
    public function execute(AMQPMessage $msg)
    {

        $deployment = $this->findDeploymentEntity($msg);

        if (is_null($deployment)) {
            return true;
        }

        $this->initWorkflowEngine($deployment);

        $this->logger->info(
            "Deploying project " . $deployment->getProject()->getName()
            . " using recipe " . $deployment->getRecipe()->getName()
        );

        $deployment->setStartDate(new \DateTime());
        $deployment->setStatus(Deployment::STATUS_DEPLOYING);
        $this->em->persist($deployment);
        $this->em->flush();

        $this->logger->info("Deploying");
        $returnValue = $this->workflowEngine->run();
        $this->logger->info("End deployment");

        $deployment->setEndDate(new \DateTime());
        $deployment->setOutput($this->workflowEngine->getOutput());
        $deployment->setStatus($returnValue ? Deployment::STATUS_DEPLOYED : Deployment::STATUS_ERROR);
        $this->em->persist($deployment);
        $this->em->flush();

        return true;
    }

    /**
     * @param Deployment $deploymentEntity
     */
    protected function initWorkflowEngine(Deployment $deploymentEntity)
    {
        $recipe = json_decode($deploymentEntity->getRecipe()->getWorkflow(), true);

        $this->workflowEngine->setEnvironment($deploymentEntity->getProject()->getUri());
        $this->workflowEngine->setRecipe($recipe);
    }

    /**
     * @param AMQPMessage $msg
     * @return Deployment|null
     */
    protected function findDeploymentEntity(AMQPMessage $msg)
    {
        $data = unserialize($msg->body);

        $id = $data['deploymentId'];
        $deployment = $this->em->getRepository('QaSystemCoreBundle:Deployment')->findOneById($id);

        if (is_null($deployment)) {
            $this->logger->info("Deployment entity not found, Id: $id");
            return null;
        }

        if ($deployment->getStatus() !== Deployment::STATUS_PENDING) {
            $this->logger->info("Deployment $id aborted, status is not pending");
            return null;
        }

        return $deployment;
    }
}
