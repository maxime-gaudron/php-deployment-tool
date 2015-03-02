<?php

namespace QaSystem\CoreBundle\DataFixtures;

use Faker\Factory;
use Faker\Provider\Lorem;
use Nelmio\Alice\ProcessorInterface;
use QaSystem\CoreBundle\Entity\Job;

class Processor implements ProcessorInterface
{

    /**
     * Processes an object before it is persisted to DB
     *
     * @param object $object instance to process
     */
    public function preProcess($object)
    {
        $this->preProcessDeployment($object);
    }

    /**
     * Processes an object before it is persisted to DB
     *
     * @param object $object instance to process
     */
    public function postProcess($object)
    {
    }

    /**
     * @param $deployment
     */
    private function preProcessDeployment($deployment)
    {
        if ($deployment instanceof Job) {
            switch ($deployment->getStatus()) {
                case Job::STATUS_RUNNING:
                case Job::STATUS_DONE:
                    $generator = Factory::create();
                    $faker = new Lorem($generator);

                    $output = '';
                    for ($i = 0; $i < 100; $i++) {
                        $output .= sprintf('<span class="info-output">%s</span>', $faker->text(50));
                    }
                    $deployment->setOutput($output);
                    break;
            }
        }
    }
}
