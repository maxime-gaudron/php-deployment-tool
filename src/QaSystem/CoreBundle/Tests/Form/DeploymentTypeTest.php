<?php

namespace QaSystem\CoreBundle\Test\Configuration;

use QaSystem\CoreBundle\Form\DeploymentType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\Form\Test\TypeTestCase;

class ConfigurationTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'test' => 'test',
            'test2' => 'test2',
        );

        $type = new DeploymentType();
        $form = $this->factory->create($type);

        $object = new TestObject();
        $object->fromArray($formData);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}