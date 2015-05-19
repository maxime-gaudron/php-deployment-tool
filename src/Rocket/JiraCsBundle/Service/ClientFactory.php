<?php

namespace Rocket\JiraCsBundle\Service;

use chobie\Jira\Api;
use chobie\Jira\Api\Authentication\Basic;
use chobie\Jira\Issues\Walker;
use Rocket\JiraCsBundle\Document\Project;

class ClientFactory
{
    /**
     * @param Project $project
     *
     * @return Api
     */
    public function getClient(Project $project) 
    {
        $auth = new Basic($project->getUsername(), $project->getPassword());

        return new Api($project->getUri(), $auth);
    }

    /**
     * @param Api $api
     *
     * @return Walker
     */
    public function getWalker(Api $api) {
        return new Walker($api);
    }
}
