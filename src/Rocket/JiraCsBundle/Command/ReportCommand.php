<?php

namespace Rocket\JiraCsBundle\Command;

use Rocket\JiraCsBundle\Document\Project;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ReportCommand extends ContainerAwareCommand
{
    const NAME = 'jira:report';

    protected function configure()
    {
        $this
            ->setName(static::NAME)
            ->setDescription('Synchronise Jira tickets');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Project $project */
        $timeReport = $this->getContainer()->get('rocket_jira_cs.issue_repository')->getTimeReportForTheCurrentWeek();

        $this->format($output, $timeReport);
    }

    /**
     * @param OutputInterface $output
     * @param                 $timeReport
     */
    protected function format(OutputInterface $output, $timeReport)
    {
        foreach ($timeReport as $week) {
            $output->writeLn('');
            $output->writeLn(
                sprintf(
                    '<error>week %s - %s: %s</error>',
                    $week['_id']['week'],
                    $week['_id']['year'],
                    $week['_id']['author']
                )
            );
            $output->writeLn('');

            foreach ($week['worklog'] as $worklog) {
                $output->writeLn(sprintf('<comment>%s:%s</comment>', $worklog['key'], $worklog['summary']));
                $output->writeLn(
                    sprintf(
                        '<info>%s-%s-%s: %s - %s</info>',
                        $worklog['day'],
                        $worklog['month'],
                        $worklog['year'],
                        $worklog['timeSpent'],
                        array_key_exists('comment', $worklog) ? $worklog['comment'] : ''
                    )
                );
            }
        }
    }
}
