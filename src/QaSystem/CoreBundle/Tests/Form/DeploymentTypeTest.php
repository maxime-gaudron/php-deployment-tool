<?php

namespace QaSystem\CoreBundle\Tests\Form;

use QaSystem\CoreBundle\Form\JobType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Acme\TestBundle\Model\TestObject;
use Symfony\Component\Form\Test\TypeTestCase;

class JobTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $this->markTestIncomplete();

        $emptyTask = array();

        $formData = array(
            'test'  => 'test',
            'test2' => 'test2',
        );

        $type = new JobType($emptyTask);
        $form = $this->factory->create($type);

        $object = new TestObject();
        $object->fromArray($formData);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());

        $view     = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}