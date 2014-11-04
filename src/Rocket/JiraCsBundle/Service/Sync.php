<?php

namespace Rocket\JiraCsBundle\Service;

use chobie\Jira\Api;
use chobie\Jira\Issue as JiraIssue;
use chobie\Jira\Issues\Walker;
use Rocket\JiraCsBundle\Document\Issue;
use Rocket\JiraCsBundle\Document\Project;
use Doctrine\ODM\MongoDB\DocumentManager;
use Rocket\JiraCsBundle\Service\ClientFactory;
use Rocket\JiraCsBundle\Document\ProjectRepository;
use Rocket\JiraCsBundle\Document\IssueRepository;

class Sync
{
    /**
     * @var ClientFactory
     */
    private $factory;

    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var IssueRepository
     */
    private $issueRepository;

    /**
     * @param ClientFactory     $factory
     * @param ProjectRepository $projectRepository
     * @param IssueRepository   $issueRepository
     * @param DocumentManager   $documentManager
     */
    public function __construct(
        ClientFactory $factory,
        ProjectRepository $projectRepository,
        IssueRepository $issueRepository,
        DocumentManager $documentManager
    ) {
        $this->factory = $factory;
        $this->documentManager = $documentManager;
        $this->projectRepository = $projectRepository;
        $this->issueRepository = $issueRepository;
    }

    /**
     * @param Project $project
     */
    public function sync(Project $project)
    {
        $api = $this->factory->getClient($project);
        $walker = $this->factory->getWalker($api);
        $query = $this->buildQuery($project);
        
        $walker->push($query);
        
        $this->persist($walker, $api);
    }

    /**
     * @param Walker $walker
     * @param Api    $api
     */
    protected function persist(Walker $walker, Api $api)
    {
        $counter = 1;
        $batchSize = 100;
        
        /** @var JiraIssue $apiIssue */
        foreach ($walker as $apiIssue) {
            echo "Syncing " . $apiIssue->getKey() . PHP_EOL;

            $this->documentManager->persist(
                $this->createDocument($api, $apiIssue)
            );

            if (($counter % $batchSize) === 0) {
                $this->documentManager->flush(); // Executes all updates.
                $this->documentManager->clear();
            }

            ++$counter;
        }
        $this->documentManager->flush();
        $this->documentManager->clear();
    }

    /**
     * @param Project $project
     *
     * @return string
     */
    protected function buildQuery(Project $project)
    {
        $lastDocument = $this->documentManager->createQueryBuilder('\Rocket\JiraCsBundle\Document\Issue')
            ->sort('updatedAt', 'desc')
            ->limit(1)
            ->getQuery()
            ->getSingleResult();

        $condition = '';

        if ($lastDocument instanceof Issue) {
            $condition = sprintf("AND updatedDate >= '%s'", $lastDocument->getUpdatedAt()->format('Y-m-d H:i'));
        }

        $query = sprintf("project = %s %s ORDER BY updatedDate ASC", $project->getName(), $condition);

        return $query;
    }

    /**
     * @param Api       $api
     * @param JiraIssue $apiIssue
     *
     * @return Issue
     */
    protected function createDocument(Api $api, JiraIssue $apiIssue)
    {
        $fields = $apiIssue->getFields();

        $issue = $this->issueRepository->findOneBy(['key' => $apiIssue->getKey()]);
        if (!$issue instanceOf Issue) {
            $issue = new Issue();
        }
        
        $issue->setFields($fields)
            ->setId($apiIssue->getId())
            ->setKey($apiIssue->getKey())
            ->setUpdatedAt($this->convertStringToMongoDate($fields['Updated']))
            ->setExpandedInformation($apiIssue->getExpandedInformation())
            ->setSelf($apiIssue->getSelf());


        $worklogs = $api->api(
            \chobie\Jira\Api::REQUEST_GET,
            sprintf('/rest/api/2/issue/%s/worklog', $apiIssue->getKey())
        )->getResult()['worklogs'];
        
        foreach ($worklogs as &$worklog) {
            if (!array_key_exists('comment', $worklog)) {
                $worklog['comment'] = '';
            }
            
            $worklog['created'] = $this->convertStringToMongoDate($worklog['created']);
            $worklog['updated'] = $this->convertStringToMongoDate($worklog['updated']);
            $worklog['started'] = $this->convertStringToMongoDate($worklog['started']);
        }
        
        $issue->setWorklogs($worklogs);

        return $issue;
    }

    /**
     * @param string $date
     *
     * @return \MongoDate
     */
    protected function convertStringToMongoDate($date)
    {
        return new \MongoDate(strtotime($date));
    }
}
