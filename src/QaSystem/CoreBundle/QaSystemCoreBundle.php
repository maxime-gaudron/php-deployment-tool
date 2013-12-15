<?php

namespace QaSystem\CoreBundle;

use Symfony\Component\Console\Application;
use QaSystem\CoreBundle\Command\PullCommand;
use QaSystem\CoreBundle\Command\DeployCommand;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use QaSystem\CoreBundle\Command\CheckoutCommand;

class QaSystemCoreBundle extends Bundle
{
    public function registerCommands(Application $application)
    {
        $application->add(new CheckoutCommand());
        $application->add(new PullCommand());
        $application->add(new DeployCommand());
    }
}
