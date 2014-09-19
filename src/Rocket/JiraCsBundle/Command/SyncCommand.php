<?php

namespace Rocket\JiraCsBundle\Command;

use Rocket\JiraCsBundle\Document\Project;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class SyncCommand extends ContainerAwareCommand
{
    const NAME = 'jira:sync';

    protected function configure()
    {
        $this
            ->setName(static::NAME)
            ->setDescription('Synchronise Jira tickets')
            ->addArgument('project-name', InputArgument::REQUIRED);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('project-name');

        /** @var Project $project */
        $project = $this->getContainer()->get('rocket_jira_cs.project_repository')
            ->findOneByName($name);

        if (is_null($project)) {
            throw new \RuntimeException(sprintf("Jira project %s not found", $name));
        }

        $this->getContainer()->get('rocket_jira_cs.sync')->sync($project);
    }
}
