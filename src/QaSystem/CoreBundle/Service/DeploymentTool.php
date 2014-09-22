<?php

namespace QaSystem\CoreBundle\Service;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use QaSystem\CoreBundle\Command\DeployCommand;
use QaSystem\CoreBundle\Git\Helper;
use QaSystem\CoreBundle\Git\Repository;
use QaSystem\CoreBundle\Workflow\Engine;
use QaSystem\CoreBundle\Entity\Deployment;
use Symfony\Component\Filesystem\Filesystem;

class DeploymentTool
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var VersionControlService
     */
    protected $versionControlService;

    /**
     * @var Helper
     */
    protected $gitHelper;

    /**
     * @var Engine
     */
    private $workflowEngine;

    /**
     * @param EntityManager         $em
     * @param LoggerInterface       $logger
     * @param Filesystem            $filesystem
     * @param VersionControlService $versionControlService
     * @param Helper                $gitHelper
     * @param Engine                $workflowEngine
     */
    public function __construct(
        EntityManager $em,
        LoggerInterface $logger,
        Filesystem $filesystem,
        VersionControlService $versionControlService,
        Helper $gitHelper,
        Engine $workflowEngine
    ) {
        $this->em                    = $em;
        $this->logger                = $logger;
        $this->filesystem            = $filesystem;
        $this->versionControlService = $versionControlService;
        $this->gitHelper             = $gitHelper;
        $this->workflowEngine        = $workflowEngine;
    }

    /**
     * @return Filesystem
     */
    protected function getFileSystem()
    {
        return $this->filesystem;
    }

    /**
     * @param Deployment $deployment
     *
     * @throws \RuntimeException
     */
    public function deploy(Deployment $deployment)
    {
        $projectName = $deployment->getProject()->getName();
        $recipeName  = $deployment->getRecipe()->getName();
        $branchName  = $deployment->getBranch();

        /** @var Repository $repository */
        $repository = $this->gitHelper->getOrCloneRepository($deployment->getProject());

        $remoteName = 'origin';

        $this->logger->info(sprintf('Reset project %s', $projectName));
        $repository->unstage($repository->getPath());

        $this->logger->info(sprintf('Fetch project %s', $projectName));
        $repository->fetch($remoteName, null, true);

        $this->logger->info(sprintf('Checkout branch %s of project %s', $branchName, $projectName));
        $repository->checkout($branchName);

        $this->logger->info(sprintf('Pull branch %s of project %s', $branchName, $projectName));
        $repository->pull($remoteName, $deployment->getBranch(), true);

        $defaultBranch = $deployment->getProject()->getDefaultBranch();
        $commitsBehind = $repository->countCommitsBehind($branchName, sprintf('origin/%s', $defaultBranch));
        $deployment->setCommitsBehind($commitsBehind);

        $this->logger->info(
            sprintf('Deploying branch "%s" project %s using recipe %s', $branchName, $projectName, $recipeName)
        );

        $deployment->setStartDate(new \DateTime());
        $deployment->setStatus(Deployment::STATUS_DEPLOYING);
        $this->em->persist($deployment);
        $this->em->flush();

        $returnValue = $this->workflowEngine->run($deployment);
        $status = $returnValue ? Deployment::STATUS_DEPLOYED : Deployment::STATUS_ERROR;

        $this->logger->info(
            sprintf(
                'End deployment branch "%s" project %s using recipe %s status: %s',
                $branchName,
                $projectName,
                $recipeName,
                $status
            )
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
}
