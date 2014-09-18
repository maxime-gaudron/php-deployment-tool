<?php

namespace QaSystem\CoreBundle\VersionControl;

use Github\Api\Repo;
use Github\Client;
use QaSystem\CoreBundle\Entity\Project;

class GitHubAdapter implements VersionControlAdapterInterface
{
    /**
     * @var \Github\Client
     */
    private $client;

    private $token;

    private $username;

    private $repository;

    public function __construct(Client $client, $token, $username, $repository)
    {
        $this->client     = $client;
        $this->token      = $token;
        $this->username   = $username;
        $this->repository = $repository;
    }

    public function getBranches(Project $project)
    {
        $token = $project->getGithubToken();
        if (null === $token) {
            $token = $this->token;
        }
        $this->client->authenticate($token, null, Client::AUTH_URL_TOKEN);

        /** @var Repo $api */
        $api = $this->client->api('repository');

        $api->setPerPage(200);
        $githubBranches = $api->branches($project->getGithubUsername(), $project->getGithubRepository());

        $branches = [];
        foreach ($githubBranches as $githubBranch) {
            $branches[$githubBranch['name']] = new Branch($githubBranch['name'], $githubBranch['commit']['sha']);
        }

        ksort($branches);

        return $branches;
    }

    /**
     * @return boolean
     */
    public function supports()
    {
        return Project::TYPE_GITHUB;
    }
}
