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
     * @var string
     */
    protected $environment;

    /**
     * @param $rootDir
     * @param $environment
     * @param Logger $logger
     */
    public function __construct($rootDir, $environment, Logger $logger)
    {
        $this->logger = $logger;
        $this->rootDir = $rootDir;
        $this->environment = $environment;
    }

    /**
     * @param $name
     * @param array $args
     */
    protected function executeCommand($name, array $args = [])
    {
        $logger = $this->logger;

        $env = $this->environment;
        $command = "php console --env=$env $name " . implode(' ', $args);

        $process = new Process($command, $this->rootDir);
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
