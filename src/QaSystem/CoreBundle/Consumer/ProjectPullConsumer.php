<?php

namespace QaSystem\CoreBundle\Consumer;

use Monolog\Logger;
use PhpAmqpLib\Message\AMQPMessage;
use QaSystem\CoreBundle\Command\PullCommand;

class ProjectPullConsumer extends AbstractConsumer
{
    /**
     * @param AMQPMessage $msg
     * @return bool
     */
    public function execute(AMQPMessage $msg)
    {
        $data = unserialize($msg->body);

        $this->executeCommand(PullCommand::NAME, [
            $data['projectId']
        ]);

        return true;
    }
}
