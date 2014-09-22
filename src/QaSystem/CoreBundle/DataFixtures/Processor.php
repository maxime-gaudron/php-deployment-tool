<?php

namespace QaSystem\CoreBundle\DataFixtures;

use Faker\Factory;
use Faker\Provider\Lorem;
use Nelmio\Alice\ProcessorInterface;
use QaSystem\CoreBundle\Entity\Deployment;

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
        if ($deployment instanceof Deployment) {
            switch ($deployment->getStatus()) {
                case Deployment::STATUS_PENDING:
                    $deployment->setStartDate(null);
                    $deployment->setEndDate(null);
                    break;
                case Deployment::STATUS_DEPLOYING:
                    $deployment->setEndDate(null);
                    break;
                case Deployment::STATUS_DEPLOYED:
                    $generator = Factory::create();
                    $faker = new Lorem($generator);

                    $output = '';
                    for ($i = 0; $i < 100; $i++) {
                        $output .= sprintf('<span class="info-output">%s</span>', $faker->text(50));
                    }
                    $deployment->setOutput($output);
                    break;
                case Deployment::STATUS_ABORTED:
                    $deployment->setEndDate(null);
                    break;
            }
        }
    }
}
