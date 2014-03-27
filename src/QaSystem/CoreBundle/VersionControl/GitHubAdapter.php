<?php

namespace QaSystem\CoreBundle\VersionControl;

use Github\Api\Repo;
use Github\Client;

class GitHubAdapter
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
        $this->client = $client;
        $this->token = $token;
        $this->username = $username;
        $this->repository = $repository;
    }

    public function getBranches()
    {
        $this->client->authenticate($this->token, null, Client::AUTH_URL_TOKEN);

        /** @var Repo $api */
        $api = $this->client->api('repository');

        return $api->branches($this->username, $this->repository);
    }
}
