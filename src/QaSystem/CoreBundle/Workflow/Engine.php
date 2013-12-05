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
    protected $steps;

    /**
     * @var array
     */
    protected $recipe;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $output;


    function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param mixed $steps
     */
    public function setSteps($steps)
    {
        $this->steps = $steps;
    }

    /**
     * @return mixed
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \LogicException
     */
    public function getStep($name)
    {
        foreach ($this->steps as $step) {
            if ($step->getName() === $name) {
                return $step;
            }
        }

        throw new \LogicException("Recipe rely on non existent step '$name'");
    }

    /**
     * @param string $environment
     * @throws LogicException
     */
    public function setEnvironment($environment)
    {
        if (is_null(realpath($environment))) {
            throw new LogicException('Non existent environment');
        }

        $this->environment = $environment;
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
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

        $stepDefinition = $this->recipe[$stepName];
        $step = $this->getStep($stepDefinition['step']);

        $this->output .= "WE > Executing step '$stepName' : " . $step->getName() . " \n";
        $command = $this->replaceCommandPlaceholder($step->getCommand());

        // Stuff to move
        $process = new Process($command);
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

            if (array_key_exists('next',$stepDefinition)) {
                return $this->makeStep($stepDefinition['next']);
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
     */
    protected function replaceCommandPlaceholder($command)
    {
        $finalCommand = str_replace("{{ENV_PATH}}", $this->environment, $command, $numberOfPlaceHolder);

        return $finalCommand;
    }
}