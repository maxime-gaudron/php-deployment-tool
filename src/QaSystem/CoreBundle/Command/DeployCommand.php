<?php

namespace QaSystem\CoreBundle\Command;

use QaSystem\CoreBundle\Entity\Deployment;
use QaSystem\CoreBundle\Service\DeploymentTool;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class DeployCommand extends ContainerAwareCommand
{
    const NAME = 'deployment:tools:deploy';

    protected function configure()
    {
        $this
            ->setName(static::NAME)
            ->setDescription('Trigger a deployments');
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
        /** @var Deployment[] $entities */
        $entities = $this->getEntityManager()
            ->getRepository('QaSystemCoreBundle:Deployment')
            ->findBy(['status' => Deployment::STATUS_PENDING]);

        foreach ($entities as $entity) {
            /** @var DeploymentTool $deploymentTool */
            $deploymentTool = $this->getContainer()->get('qa_system_core.deployment_tool');
            $deploymentTool->deploy($entity);
        }
    }
}
