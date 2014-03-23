<?php

namespace QaSystem\CoreBundle\Service;

use Monolog\Logger;
use Doctrine\ORM\EntityManager;
use QaSystem\CoreBundle\Command\DeployCommand;
use QaSystem\CoreBundle\Entity\Project;
use QaSystem\CoreBundle\Workflow\Engine;
use QaSystem\CoreBundle\Entity\Deployment;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class DeploymentTool
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @param EntityManager $em
     * @param Logger $logger
     */
    public function __construct(EntityManager $em, Logger $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->filesystem = new Filesystem();
    }

    /**
     * @return Filesystem
     */
    protected function getFileSystem()
    {
        return $this->filesystem;
    }

    /**
     * @param Project $project
     * @param $branch
     */
    public function checkout(Project $project, $branch)
    {
        $this->reset($project);
        $project->getRepository()->checkout($branch);
        $this->rebase($project);

        $this->logger->info("Checkout branch $branch of project " . $project->getName());
    }

    /**
     * @param Project $project
     */
    public function update(Project $project)
    {
        $this->reset($project);
        $this->rebase($project);
        $project->getRepository()->checkoutAllRemoteBranches();

        $this->logger->info("Updated project " . $project->getName());
    }

    /**
     * @param Deployment $deployment
     */
    public function deploy(Deployment $deployment)
    {
        $recipe = json_decode($deployment->getRecipe()->getWorkflow(), true);

        $workflowLogger = new \QaSystem\CoreBundle\Workflow\Logger($this->em, $deployment);
        $workflowEngine = new Engine($workflowLogger);

        $workflowEngine->addVariable('environment', $deployment->getProject()->getUri());
        foreach (json_decode($deployment->getProject()->getVariables(), true) as $key => $value) {
            $workflowEngine->addVariable($key, $value);
        }

        $workflowEngine->setRecipe($recipe);

        $this->logger->info(
            "Deploying project " . $deployment->getProject()->getName()
            . " using recipe " . $deployment->getRecipe()->getName()
        );

        $deployment->setStartDate(new \DateTime());
        $deployment->setStatus(Deployment::STATUS_DEPLOYING);
        $this->em->persist($deployment);
        $this->em->flush();

        $returnValue = $workflowEngine->run();
        $status = $returnValue ? Deployment::STATUS_DEPLOYED : Deployment::STATUS_ERROR;

        $this->logger->info(
            "End deployment project " . $deployment->getProject()->getName()
            . " using recipe " . $deployment->getRecipe()->getName()
            . " status: $status"
        );

        $deployment->setEndDate(new \DateTime());
        $deployment->setStatus($status);

        $this->em->persist($deployment);
        $this->em->flush();
    }

    /**
     * @param Deployment $deployment
     */
    public function abort(Deployment $deployment)
    {
        $filesystem = $this->getFileSystem();
        $pidFile = DeployCommand::getPidfilePath($deployment->getId());

        if ($filesystem->exists($pidFile)) {
            $pid = file_get_contents($pidFile);

            exec("kill -9 $pid", $output);

            $filesystem->remove($pidFile);

            $deployment->setStatus(Deployment::STATUS_ABORTED);
            $this->em->persist($deployment);
            $this->em->flush();
        }
    }

    /**
     * @param Project $project
     */
    protected function rebase(Project $project)
    {
        $this->runCommand($project->getUri(), 'git pull --rebase');
    }

    /**
     * @param Project $project
     */
    protected function reset(Project $project)
    {
        $this->runCommand($project->getUri(), 'git reset --hard HEAD');
    }

    /**
     * @param string $uri
     * @param string $command
     */
    protected function runCommand($uri, $command)
    {
        $command = sprintf('cd %s && %s', $uri, $command);
        $process = new Process($command);
        $process->run();

        $this->logger->info($process->getOutput());
    }
}
