<?php

namespace QaSystem\CoreBundle\Consumer;

use Monolog\Logger;
use PhpAmqpLib\Message\AMQPMessage;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use Cypress\GitElephantBundle\Collection\GitElephantRepositoryCollection;

class ProjectCheckoutConsumer implements ConsumerInterface
{

    /**
     * @var \Cypress\GitElephantBundle\Collection\GitElephantRepositoryCollection
     */
    protected $repositories;

    /**
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * @param Logger $logger
     * @param GitElephantRepositoryCollection $repositories
     */
    function __construct(GitElephantRepositoryCollection $repositories, Logger $logger)
    {
        $this->logger = $logger;
        $this->repositories = $repositories;
    }

    public function execute(AMQPMessage $msg)
    {
        $data = unserialize($msg->body);
        $branch = $data['branch'];
        $name = $data['name'];

        $repository = $this->repositories->get($name);
        $repository->checkout($branch);

        $this->logger->info("Checkout branch $branch of project $name");
    }
}
