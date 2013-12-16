<?php

namespace QaSystem\CoreBundle\Consumer;

use Monolog\Logger;
use Symfony\Component\Process\Process;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;

abstract class AbstractConsumer implements ConsumerInterface
{

    /**
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @param $rootDir
     * @param Logger $logger
     */
    function __construct($rootDir, Logger $logger)
    {
        $this->logger = $logger;
        $this->rootDir = $rootDir;
    }

    /**
     * @param $name
     * @param array $args
     */
    protected function executeCommand($name, array $args = [])
    {
        $logger = $this->logger;
        $rootDir = $this->rootDir;

        $command = "php $rootDir/console $name " . implode(' ', $args);

        $process = new Process($command);
        $process->setTimeout(null);
        $process->run(function ($type, $buffer) use ($logger) {
            if (Process::ERR === $type) {
                $logger->err($buffer);
            } else {
                $logger->info($buffer);
            }
        });
    }
}
