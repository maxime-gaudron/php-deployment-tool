<?php

namespace QaSystem\CoreBundle\Consumer;

use Monolog\Logger;
use PhpAmqpLib\Message\AMQPMessage;
use QaSystem\CoreBundle\Command\DeployCommand;

class ProjectDeployConsumer extends AbstractConsumer
{
    /**
     * @param AMQPMessage $msg
     * @return bool
     */
    public function execute(AMQPMessage $msg)
    {
        $data = unserialize($msg->body);

        $this->executeCommand(DeployCommand::NAME, [
            $data['deploymentId']
        ]);

        return true;
    }
}
