<?php

namespace QaSystem\CoreBundle\VersionControl;

use QaSystem\CoreBundle\Entity\Project;

interface VersionControlAdapterInterface
{

    /**
     * @param Project $project
     * @return mixed
     */
    public function getBranches(Project $project);

    /**
     * @param Project $project
     * @param $branchName
     *
     * @return
     */
    public function checkoutBranch(Project $project, $branchName);

    /**
     * @return boolean
     */
    public function supports();
}
