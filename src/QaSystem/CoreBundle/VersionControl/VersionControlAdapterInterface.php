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
     * @return boolean
     */
    public function supports();
}
