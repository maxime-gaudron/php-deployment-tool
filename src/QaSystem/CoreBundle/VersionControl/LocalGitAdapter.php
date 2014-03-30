<?php

namespace QaSystem\CoreBundle\VersionControl;

use GitElephant\Repository;
use GitElephant\Objects\Branch as GitBranch;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use QaSystem\CoreBundle\Entity\Project;

class LocalGitAdapter implements VersionControlAdapterInterface
{

    private $projectCheckoutProducer;

    public function __construct(Producer $projectCheckoutProducer)
    {
        $this->projectCheckoutProducer = $projectCheckoutProducer;
    }

    /**
     * @param Project $project
     * @return array
     */
    public function getBranches(Project $project)
    {
        $localBranches = $this->getRepository($project)->getBranches();

        $branches = [];
        foreach ($localBranches as $localBranch) {
            /** @var GitBranch $localBranch */
            $branchName = $localBranch->getName();
            $branches[$branchName] = new Branch($branchName, $localBranch->getSha());
        }
        ksort($branches);

        return $branches;
    }

    /**
     * @param Project $project
     * @param $branchName
     */
    public function checkoutBranch(Project $project, $branchName)
    {
        $this->getRepository($project)->checkout($branchName);
    }

    /**
     * @return boolean
     */
    public function supports()
    {
        return 'local';
    }

    /**
     * @param Project $project
     * @return Repository
     */
    private function getRepository(Project $project)
    {
        return Repository::open($project->getUri());
    }
}
