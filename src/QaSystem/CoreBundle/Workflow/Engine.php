<?php

namespace QaSystem\CoreBundle\Workflow;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

/**
 * Class Engine
 * @package QaSystem\CoreBundle\Workflow
 *
 * @TODO: Create interfaces for step / recipe
 * @TODO: Rename step / recipe to an understandable name
 */
class Engine {

    /**
     * @var array
     */
    protected $recipe;

    /**
     * @var \Monolog\Logger
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


    function __construct(LoggerInterface $logger)
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
     * @return bool
     * @throws \LogicException
     */
    public function run()
    {
        if (!array_key_exists('start', $this->recipe)) {
            throw new \LogicException('Recipe has no starting point');
        }

        $this->output .= "WE > Starting \n";

        return $this->makeStep($this->recipe['start']);
    }

    /**
     * @param $stepName string
     * @return bool
     * @throws \LogicException
     */
    protected function makeStep($stepName)
    {
        if (!array_key_exists($stepName, $this->recipe)) {
            throw new \LogicException("Step definition '$stepName' non existent in recipe");
        }

        $step = $this->recipe[$stepName];

        $this->output .= "WE > Executing step '$stepName' : " . $step['name'] . " \n";

        try {
            $command = $this->replaceCommandPlaceholder($step['command']);
        } catch (\RuntimeException $exception) {
            $this->output .= "ERR > " . $exception->getMessage() . "\n";
            return false;
        }

        // Stuff to move
        $process = new Process($command);
        $process->setTimeout(null);
        $process->run(function ($type, $buffer) {
            $this->output .= (Process::ERR === $type) ? 'ERR > ' : 'OUT > ';
            $this->output .= $buffer . "\n";
        });

        if (!$process->isSuccessful()) {
            $this->output .= "WE > Step '$stepName' failed\n";
            $this->output .= "WE > Recipe failed\n";
            return false;
        } else {
            $this->output .= "WE > Step '$stepName' successful\n";

            if (array_key_exists('next',$step)) {
                return $this->makeStep($step['next']);
            } else {
                $this->output .= "WE > Recipe ended successfully\n";
                return true;
            }
        }
    }

    /**
     * Replace placeholder in command
     *
     * @param $command
     * @return mixed
     * @throws \RuntimeException
     */
    protected function replaceCommandPlaceholder($command)
    {
        $finalCommand = str_replace("{{ENV_PATH}}", $this->variables['environment'], $command);

        $currentBranch = \GitElephant\Repository::open($this->variables['environment'])->getMainBranch()->getName();
        $finalCommand = str_replace("{{CURRENT_BRANCH}}", $currentBranch, $finalCommand);

        // get host name from URL
        preg_match_all('@{{(.*?)}}@i',
            $finalCommand, $matches);

        foreach ($matches[1] as $key) {
            if (!array_key_exists($key, $this->variables)) {
                throw new \RuntimeException(sprintf('Variable "%s" not found, aborting', $key));
            }

            $finalCommand = str_replace("{{" .$key . "}}", $this->variables[$key], $finalCommand);
        }

        return $finalCommand;
    }
}