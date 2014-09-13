<?php

namespace QaSystem\CoreBundle\Service;

use QaSystem\CoreBundle\Entity\Project;
use QaSystem\CoreBundle\VersionControl\Branch;
use QaSystem\CoreBundle\VersionControl\VersionControlAdapterInterface;

class VersionControlService
{
    private $adapters;

    /**
     * @param VersionControlAdapterInterface[] $adapters
     */
    public function __construct(array $adapters)
    {
        $this->adapters = $adapters;
    }

    /**
     * @param Project $project
     *
     * @return Branch[]
     */
    public function getBranches(Project $project)
    {
        $branches = $this->getAdapter($project)->getBranches($project);

        return $branches;
    }

    /**
     * @param Project $project
     * @return VersionControlAdapterInterface
     * @throws \RuntimeException
     */
    private function getAdapter(Project $project)
    {
        $type = $project->getType();

        foreach ($this->adapters as $adapter) {
            if ($type === $adapter->supports()) {
                return $adapter;
            }
        }

        throw new \RuntimeException(sprintf('Type "%s" is not supported.', $type));
    }
}
