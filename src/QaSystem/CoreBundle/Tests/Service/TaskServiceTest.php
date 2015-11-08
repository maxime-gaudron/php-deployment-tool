<?php

namespace QaSystem\CoreBundle\Tests\Service;

use QaSystem\CoreBundle\Service\TaskService;

class TaskServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testFindAll()
    {
        $expected    = $this->getEchoTask();
        $rootPath =  __DIR__ . '/../Fixtures';
        $taskService = new TaskService($rootPath);
        $tasks       = $taskService->getAll();
        $this->assertEquals($expected, $tasks);
    }

    /**
     * @return array
     */
    private function getEchoTask()
    {
        return [
            'tasks' => [
                'echo' => [
                    'name'       => 'echo',
                    'command'    => 'echo {{text}}',
                    'parameters' => [
                        'text' => [
                            'name'   => 'Text to print',
                            'code'   => 'text',
                            'type'   => 'choice',
                            'values' => [
                                'foo'         => 'foo',
                                'world'       => 'world',
                                'Hello world' => 'Hello world',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
