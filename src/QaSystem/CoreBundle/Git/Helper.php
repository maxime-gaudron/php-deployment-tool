<?php

namespace QaSystem\CoreBundle\Git;

use QaSystem\CoreBundle\Entity\Project;
use Symfony\Component\Filesystem\Filesystem;

class Helper
{
    /**
     * @var string
     */
    private $repositoryRootDir;

    private $fileSystem;

    /**
     * @param string     $repositoryRootDir
     * @param Filesystem $filesystem
     */
    function __construct($repositoryRootDir, Filesystem $filesystem)
    {
        $this->repositoryRootDir = $repositoryRootDir;
        $this->fileSystem        = $filesystem;
    }

    /**
     * @param Project $project
     *
     * @throws \InvalidArgumentException
     * @return Repository
     */
    public function getOrCloneRepository(Project $project)
    {
        if (!($project->getGithubUsername() && $project->getGithubRepository())) {
            throw new \InvalidArgumentException(sprintf('Github username/repository are missing for project "%s"', $project->getName()));
        }

        $repositoryDir = sprintf('%s/%s', $this->repositoryRootDir, $project->getGithubRepository());
        if (!$this->fileSystem->exists($repositoryDir)) {
            $this->fileSystem->mkdir($repositoryDir);
            $repository = $this->cloneRepository($project, $repositoryDir);
        } else {
            $repository = new Repository($repositoryDir);
        }

        return $repository;
    }

    /**
     * @param Project $project
     * @param         $repositoryDir
     *
     * @return Repository
     */
    protected function cloneRepository(Project $project, $repositoryDir)
    {
        $repository = new Repository($repositoryDir);

        $repositoryUrl = sprintf(
            'git@github.com:%s/%s.git',
            $project->getGithubUsername(),
            $project->getGithubRepository()
        );
        $repository->cloneFrom($repositoryUrl, $repositoryDir);

        return $repository;
    }
}
