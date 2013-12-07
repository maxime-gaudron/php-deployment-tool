<?php

namespace QaSystem\CoreBundle\Consumer;

use Monolog\Logger;
use Doctrine\ORM\EntityManager;
use PhpAmqpLib\Message\AMQPMessage;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;

class ProjectCheckoutConsumer implements ConsumerInterface
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * @param EntityManager $em
     * @param Logger $logger
     */
    function __construct(EntityManager $em, Logger $logger)
    {
        $this->logger = $logger;
        $this->em = $em;
    }

    public function execute(AMQPMessage $msg)
    {
        $data = unserialize($msg->body);
        $branch = $data['branch'];
        $id = $data['projectId'];

        $project = $this->em->getRepository('QaSystemCoreBundle:Project')->findOneById($id);

        if (is_null($project)) {
            $this->logger->info("Project entity not found, Id: $id");
            return true;
        }

        $project->getRepository()->checkout($branch);

        $this->logger->info("Checkout branch $branch of project " . $project->getName());
        return true;
    }
}
