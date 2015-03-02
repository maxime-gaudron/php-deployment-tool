<?php

namespace QaSystem\CoreBundle\Command;

use QaSystem\CoreBundle\Entity\Job;
use QaSystem\CoreBundle\Service\DeploymentTool;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class JobCommand extends ContainerAwareCommand
{
    const NAME = 'job:run';

    protected function configure()
    {
        $this
            ->setName(static::NAME)
            ->setDescription('Trigger a job');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Job[] $entities */
        $entities = $this->getEntityManager()
            ->getRepository('QaSystemCoreBundle:Job')
            ->findBy(['status' => Job::STATUS_PENDING]);

        foreach ($entities as $entity) {
            /** @var DeploymentTool $deploymentTool */
            $deploymentTool = $this->getContainer()->get('qa_system_core.deployment_tool');
            $deploymentTool->run($entity);
        }
    }
}
