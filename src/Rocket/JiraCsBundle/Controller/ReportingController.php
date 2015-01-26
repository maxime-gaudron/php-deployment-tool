<?php

namespace Rocket\JiraCsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Reporting controller.
 *
 * @Route("/jira/reporting")
 */
class ReportingController extends Controller
{

    /**
     * Worklog reporting.
     *
     * @Route("/worklogs/{author}/{from}/{to}",
     *          defaults={"author" = "", "from" = "", "to" = ""},
     *          name="jira_reporting_worklog", options={"expose"=true})
     * @Method("GET")
     * @Template()
     */
    public function worklogsAction($author, $from, $to)
    {
        $docs = array();
        if (strlen($author) > 0) {
            $docs = $this->fetchWorkLogs($author, $from, $to);
        }

        $authors = $this->get('rocket_jira_cs.issue_repository')
            ->getAuthors()
            ->toArray();
        sort($authors);

        return array(
            'docs' => $docs,
            'authors' => $authors
        );
    }

    /**
     * Worklog reporting.
     *
     * @Route("/api/{author}/{from}/{to}", name="jira_reporting_worklog_json", options={"expose"=true})
     * @Method("GET")
     *
     * @param $author
     *
     * @param $from
     * @param $to
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function worklogsJsonAction($author, $from, $to)
    {
        $docs = $this->fetchWorkLogs($author, $from, $to);

        $response = new Response(json_encode($docs));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param $author
     * @param $from
     * @param $to
     *
     * @return array
     */
    protected function fetchWorkLogs($author, $from, $to)
    {
        $from  = \DateTime::createFromFormat('d-m-Y', $from);
        $to    = \DateTime::createFromFormat('d-m-Y', $to);
        $weeks = $this->get('rocket_jira_cs.issue_repository')
            ->getTimeReportBy($author, $from, $to)
            ->toArray();

        $docs = array();
        foreach ($weeks as $doc) {
            $doc['id'] = sprintf('%s/%s/%s', $doc['_id']['day'], $doc['_id']['month'], $doc['_id']['year']);
            unset($doc['_id']);
            $docs[] = $doc;
        }

        return $docs;
    }
}
