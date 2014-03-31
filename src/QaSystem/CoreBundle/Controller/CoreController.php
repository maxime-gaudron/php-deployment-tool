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

        return [
            'projects' => $projects,
        ];
    }

    /**
     * @Route("/checkout/{id}", name="project_pull")
     */
    public function pullAction($id)
    {
        $msg = array(
            'projectId' => $id
        );

        $this->get('old_sound_rabbit_mq.project_pull_producer')->publish(serialize($msg));

        return $this->redirect(
            $this->generateUrl(
                'project_show',
                array('id' => $id)
            )
        );
    }
}
