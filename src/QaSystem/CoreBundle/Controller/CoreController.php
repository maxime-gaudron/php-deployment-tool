<?php

namespace QaSystem\CoreBundle\Controller;

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
        $deployments = $em->getRepository('QaSystemCoreBundle:Deployment')->findBy([], ['id' => 'desc']);

        return [
            'projects' => $projects,
            'deployments' => $deployments
        ];
    }
}
