<?php

namespace QaSystem\CoreBundle\Service;

use QaSystem\CoreBundle\VersionControl\GitHubAdapter;

class VersionControlService
{
    private $adapter;

    function __construct(GitHubAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getBranches()
    {
        return $this->adapter->getBranches();
    }
}
