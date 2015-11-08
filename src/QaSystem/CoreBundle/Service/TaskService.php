<?php

namespace QaSystem\CoreBundle\Service;

use QaSystem\CoreBundle\TaskConfiguration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class TaskService
{
    /**
     * @var string
     */
    private $tasksPath;

    /**
     * TaskService constructor.
     *
     * @param string $tasksPath
     */
    public function __construct($tasksPath)
    {
        $this->tasksPath = $tasksPath;
    }

    public function getAll()
    {
        $finder = new Finder();

        $configuration = new TaskConfiguration();
        $processor = new Processor();

        $configs = [];
        foreach ($finder->files()->in($this->tasksPath)->name('*.yml') as $file) {
            try {
                $configs[] = Yaml::parse($file->getContents());
            } catch (ParseException $e) {
                // do nothing
            }
        }

        $tasks = [];
        if (count($configs)) {
            $tasks = $processor->processConfiguration($configuration, $configs);
        }

        return $tasks;
    }
}
