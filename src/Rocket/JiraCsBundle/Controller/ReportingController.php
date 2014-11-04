<?php

namespace Rocket\JiraCsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
     * @Route("/worklogs", name="jira_reporting_worklog")
     * @Method("GET")
     * @Template()
     */
    public function worklogsAction()
    {
        $weeks = $this->get('rocket_jira_cs.issue_repository')->getTimeReportForTheCurrentWeek();
        
        return array(
            'weeks' => array_reverse($weeks->toArray())
        );
    }
}
