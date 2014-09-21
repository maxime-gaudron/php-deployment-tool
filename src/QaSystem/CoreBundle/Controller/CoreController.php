<?php

namespace QaSystem\CoreBundle\Controller;

use QaSystem\CoreBundle\Entity\Deployment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class CoreController
 * @package QaSystem\CoreBundle\Controller
 *
 * @Route("/")
 */
class CoreController extends Controller
{

    /**
     * @Route("/", name="homepage")
     * @Template("QaSystemCoreBundle:Core:index.html.twig")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $projects = $em->getRepository('QaSystemCoreBundle:Project')->findAll();

        $status = $this->get('request')->query->get('status', Deployment::STATUS_DEPLOYED);

        $query = $em->getRepository('QaSystemCoreBundle:Server')
            ->createQueryBuilderForPagination($status)
            ->getQuery();

        $paginator  = $this->get('knp_paginator');
        $servers = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            10,
            ['distinct' => true]
        );

        return [
            'projects' => $projects,
            'servers' => $servers,
            'selectedStatus' => $status,
        ];
    }
}
