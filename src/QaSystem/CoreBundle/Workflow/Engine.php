<?php

namespace QaSystem\CoreBundle\Workflow;

use Symfony\Component\Process\Process;
use QaSystem\CoreBundle\Entity\Job;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;

/**
 * Class Engine
 * @package QaSystem\CoreBundle\Workflow
 */
class Engine
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param Logger $logger
     * @param string $repositoryRootDir
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Job $deployment
     *
     * @throws \RuntimeException
     *
     * @return bool
     */
    public function run(Job $deployment)
    {
        $logger = $this->logger;

        $command = $this->replacePlaceholders($deployment->getCommand(), $deployment->getParams());
        $logger->info(sprintf('Executing command "%s": ', $command), $deployment);

        $process = new Process($command);
        $process->setTimeout(null);

        $converter = new AnsiToHtmlConverter();
        $process->run(
            function ($type, $buffer) use ($logger, $converter, $deployment) {
                $buffer = $converter->convert($buffer);
                Process::ERR === $type
                    ? $logger->error($buffer, $deployment)
                    : $logger->info($buffer, $deployment);
            }
        );

        if (!$process->isSuccessful()) {
            $logger->error("Job failed", $deployment);

            return false;
        } else {
            $logger->info("Job ended successfully", $deployment);

            return true;
        }
    }

    /**
     * @param string $command
     * @param array $variables
     *
     * @throws \RuntimeException
     * @return string
     */
    protected function replacePlaceholders($command, array $variables)
    {
        preg_match_all('@{{(.*?)}}@i', $command, $matches);

        foreach ($matches[1] as $key) {
            if (!array_key_exists($key, $variables)) {
                throw new \RuntimeException(sprintf('Variable "%s" not found, aborting', $key));
            }

            $command = str_replace(sprintf('{{%s}}', $key), $variables[$key], $command);
        }

        return $command;
    }
}
