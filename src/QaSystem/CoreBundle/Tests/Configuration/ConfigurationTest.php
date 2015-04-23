<?php

namespace QaSystem\CoreBundle\Test\Configuration;

use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;
use QaSystem\CoreBundle\DependencyInjection\Configuration;

class ConfigurationTest extends AbstractConfigurationTestCase
{
    protected function getConfiguration()
    {
        return new Configuration();
    }

    /**
     * @test
     */
    public function tasksAreRequired()
    {
        $this->assertConfigurationIsInvalid(
            array(
                array()
            ),
            'tasks'
        );
    }

    /**
     * @test
     */
    public function taskShouldHaveName()
    {
        $this->assertConfigurationIsInvalid(
            array(
                array(
                    'tasks' => array(
                        'foo' => array(
                        )
                    )
                )
            ),
            'name'
        );
    }

    /**
     * @test
     */
    public function taskShouldHaveCommand()
    {
        $this->assertConfigurationIsInvalid(
            array(
                array(
                    'tasks' => array(
                        'foo' => array(
                            'name' => 'bar',
                        )
                    )
                )
            ),
            'command'
        );
    }

    /**
     * @test
     */
    public function tasksParameterShouldHaveName()
    {
        $this->assertConfigurationIsInvalid(
            array(
                array(
                    'tasks' => array(
                        'foo' => array(
                            'name' => 'bar',
                            'command' => 'bar',
                            'parameters' => array(
                                'foo' => array(

                                )
                            )
                        )
                    )
                )
            ),
            'name'
        );
    }

    /**
     * @test
     */
    public function tasksParameterShouldHaveACode()
    {
        $this->assertConfigurationIsInvalid(
            array(
                array(
                    'tasks' => array(
                        'foo' => array(
                            'name' => 'bar',
                            'command' => 'bar',
                            'parameters' => array(
                                'foo' => array(
                                    'name' => 'bar',
                                )
                            )
                        )
                    )
                )
            ),
            'code'
        );
    }

    /**
     * @test
     */
    public function tasksParameterShouldHaveAType()
    {
        $this->assertConfigurationIsInvalid(
            array(
                array(
                    'tasks' => array(
                        'foo' => array(
                            'name' => 'bar',
                            'command' => 'bar',
                            'parameters' => array(
                                'foo' => array(
                                    'name' => 'bar',
                                    'code' => 'bar',
                                )
                            )
                        )
                    )
                )
            ),
            'type'
        );
    }

    /**
     * @test
     */
    public function tasksParameterTypeDoesntAcceptArrayWithoutValues()
    {
        $this->assertConfigurationIsInvalid(
            array(
                array(
                    'tasks' => array(
                        'foo' => array(
                            'name' => 'bar',
                            'command' => 'bar',
                            'parameters' => array(
                                'foo' => array(
                                    'name' => 'bar',
                                    'code' => 'bar',
                                    'type' => 'choice',
                                )
                            )
                        )
                    )
                )
            ),
            'Missing values for "array" parameter type.'
        );
    }

    /**
     * @test
     */
    public function tasksParameterTypeScriptWithScriptIsValid()
    {
        $this->assertConfigurationIsValid(
            array(
                array(
                    'tasks' => array(
                        'foo' => array(
                            'name' => 'bar',
                            'command' => 'bar',
                            'parameters' => array(
                                'foo' => array(
                                    'name'   => 'bar',
                                    'code'   => 'bar',
                                    'type'   => 'script',
                                    'script' => 'random',
                                )
                            )
                        )
                    )
                )
            ),
            'Missing values for "script" parameter type.'
        );
    }
    
    /**
     * @test
     */
    public function tasksParameterTypeAcceptArrayWithValues()
    {
        $this->assertConfigurationIsValid(
            array(
                array(
                    'tasks' => array(
                        'foo' => array(
                            'name' => 'bar',
                            'command' => 'bar',
                            'parameters' => array(
                                'foo' => array(
                                    'name' => 'bar',
                                    'code' => 'bar',
                                    'type' => 'choice',
                                    'values' => array('bar')
                                )
                            )
                        )
                    )
                )
            )
        );
    }
}
