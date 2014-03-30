<?php

namespace QaSystem\CoreBundle\Workflow;

use QaSystem\CoreBundle\Entity\Deployment;
use Symfony\Component\Process\Process;

/**
 * Class Engine
 * @package QaSystem\CoreBundle\Workflow
 *
 * @TODO: Create interfaces for step / recipe
 * @TODO: Rename step / recipe to an understandable name
 */
class Engine
{
    /**
     * @var array
     */
    protected $recipe;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @var string
     */
    protected $output;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param $key
     * @param $value
     */
    public function addVariable($key, $value)
    {
        $this->variables[$key] = $value;
    }

    /**
     * @param array $recipe
     */
    public function setRecipe($recipe)
    {
        $this->recipe = $recipe;
    }

    /**
     * @return array
     */
    public function getRecipe()
    {
        return $this->recipe;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param Deployment $deployment
     * @throws \RuntimeException
     *
     * @return bool
     */
    public function run(Deployment $deployment)
    {
        $this->recipe = json_decode($deployment->getRecipe()->getWorkflow(), true);

        if (json_last_error() > JSON_ERROR_NONE) {
            throw new \RuntimeException(sprintf('Recipe JSON malformed. Error code: %d', json_last_error()));
        }

        if (!array_key_exists('start', $this->recipe)) {
            throw new \RuntimeException('Recipe has no starting point');
        }

        $this->logger->info("Starting");

        return $this->makeStep($this->recipe['start'], $deployment);
    }

    /**
     * @param $stepName string
     * @param Deployment $deployment
     * @throws \LogicException
     *
     * @return bool
     */
    protected function makeStep($stepName, Deployment $deployment)
    {
        $logger = $this->logger;

        if (!array_key_exists($stepName, $this->recipe)) {
            throw new \LogicException("Step definition '$stepName' non existent in recipe");
        }

        $step = $this->recipe[$stepName];

        $logger->info("Executing step '$stepName' : " . $step['name']);

        try {
            $command = $this->replaceCommandPlaceholder($step['command'], $deployment);
        } catch (\RuntimeException $exception) {
            $logger->info($exception->getMessage());

            return false;
        }

        // Stuff to move
        $cwd = $deployment->getProject()->getUri();
        $process = new Process($command, $cwd);
        $process->setTimeout(null);
        $process->run(
            function ($type, $buffer) use ($logger) {
                Process::ERR === $type ? $logger->error($buffer) : $logger->info($buffer);
            }
        );

        if (!$process->isSuccessful()) {
            $logger->error("Step '$stepName' failed");
            $logger->error("Recipe failed");
            return false;
        } else {
            $logger->info("Step '$stepName' successful");

            if (array_key_exists('next', $step)) {
                return $this->makeStep($step['next'], $deployment);
            } else {
                $logger->info("Recipe ended successfully");

                return true;
            }
        }
    }

    /**
     * Replace placeholder in command
     *
     * @param $command
     * @param Deployment $deployment
     * @throws \RuntimeException
     *
     * @return mixed
     */
    protected function replaceCommandPlaceholder($command, Deployment $deployment)
    {
        $finalCommand = str_replace("{{ENV_PATH}}", $deployment->getProject()->getUri(), $command);
        $finalCommand = str_replace("{{CURRENT_BRANCH}}", $deployment->getBranch(), $finalCommand);

        // get host name from URL
        preg_match_all(
            '@{{(.*?)}}@i',
            $finalCommand,
            $matches
        );

        foreach ($matches[1] as $key) {
            if (!array_key_exists($key, $this->variables)) {
                throw new \RuntimeException(sprintf('Variable "%s" not found, aborting', $key));
            }

            $finalCommand = str_replace("{{" . $key . "}}", $this->variables[$key], $finalCommand);
        }

        return $finalCommand;
    }
}
