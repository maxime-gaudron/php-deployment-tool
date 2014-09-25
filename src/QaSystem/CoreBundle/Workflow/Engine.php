<?php

namespace QaSystem\CoreBundle\Workflow;

use QaSystem\CoreBundle\Entity\Deployment;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
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
     * @var string
     */
    protected $output;

    /**
     * @var string
     */
    private $repositoryRootDir;

    /**
     * @param Logger $logger
     * @param string $repositoryRootDir
     */
    public function __construct(Logger $logger, $repositoryRootDir)
    {
        $this->logger            = $logger;
        $this->repositoryRootDir = $repositoryRootDir;
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

        $this->logger->info("Starting", $deployment);

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

        $logger->info(sprintf('Executing step "%s": %s', $stepName, $step['name']), $deployment);

        try {
            $command = $this->replaceCommandPlaceholder($step['command'], $deployment);
        } catch (\RuntimeException $exception) {
            $logger->info($exception->getMessage(), $deployment);

            return false;
        }

        $converter = new AnsiToHtmlConverter();
        // Stuff to move
        $cwd = sprintf('%s/%s', $this->repositoryRootDir, $deployment->getProject()->getGithubRepository());

        $env = array_replace($_ENV, $_SERVER, ['BRANCH_NAME' => $deployment->getBranch()]);
        
        $process = new Process($command, $cwd, $env);
        
        $process->setTimeout(null);

        $logger->info(sprintf('Executing command "%s": ', $command), $deployment);
        $process->run(
            function ($type, $buffer) use ($logger, $converter, $deployment) {
                $buffer = $converter->convert($buffer);
                Process::ERR === $type ? $logger->error($buffer, $deployment) : $logger->info($buffer, $deployment);
            }
        );

        if (!$process->isSuccessful()) {
            $logger->error("Step '$stepName' failed", $deployment);
            $logger->error("Recipe failed", $deployment);

            return false;
        } else {
            $logger->info("Step '$stepName' successful", $deployment);

            if (array_key_exists('next', $step)) {
                return $this->makeStep($step['next'], $deployment);
            } else {
                $logger->info("Recipe ended successfully", $deployment);

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
        $command = str_replace("{{SERVER}}", $deployment->getServer()->getName(), $command);

        $variables = $deployment->getProject()->getVariables();

        // get host name from URL
        preg_match_all(
            '@{{(.*?)}}@i',
            $command,
            $matches
        );

        foreach ($matches[1] as $key) {
            if (!array_key_exists($key, $variables)) {
                throw new \RuntimeException(sprintf('Variable "%s" not found, aborting', $key));
            }

            $command = str_replace(sprintf('{{%s}}', $key), $variables[$key], $command);
        }

        return $command;
    }
}
