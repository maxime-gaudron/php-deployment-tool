<?php

namespace QaSystem\CoreBundle\Consumer;

use Monolog\Logger;
use PhpAmqpLib\Message\AMQPMessage;
use QaSystem\CoreBundle\Command\CheckoutCommand;

class ProjectCheckoutConsumer extends AbstractConsumer
{

    /**
     * @param AMQPMessage $msg
     * @return bool
     */
    public function execute(AMQPMessage $msg)
    {
        $data = unserialize($msg->body);

        $this->executeCommand(CheckoutCommand::NAME, [
            $data['projectId'],
            $data['branch']
        ]);

        return true;
    }
}
