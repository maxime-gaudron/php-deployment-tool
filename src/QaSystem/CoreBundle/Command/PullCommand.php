<?php

namespace QaSystem\CoreBundle\Command;

use Monolog\Logger;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class PullCommand extends ContainerAwareCommand
{
    const NAME = 'deployment:tools:pull';

    protected function configure()
    {
        $this
            ->setName(static::NAME)
            ->setDescription('Pull a project')
            ->addArgument('projectId', InputArgument::REQUIRED);
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
        $projectId = $input->getArgument('projectId');

        $project = $this->getEntityManager()
            ->getRepository('QaSystemCoreBundle:Project')
            ->findOneById($projectId);

        if (is_null($project)) {
            throw new \RuntimeException("Project $projectId not found");
        }

        $this->getContainer()->get('deployment_tool')->pull($project);
    }
}
