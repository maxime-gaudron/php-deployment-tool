<?php

namespace QaSystem\CoreBundle\Controller;

use QaSystem\CoreBundle\Entity\Project;
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
        return [];
    }

    /**
     * @Route("/checkout/{id}/{branch}", name="project_checkout")
     * @Template()
     */
    public function checkoutAction($id, $branch)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QaSystemCoreBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $msg = array(
            'id' => $id,
            'branch' => urldecode($branch))
        ;
        $this->get('old_sound_rabbit_mq.project_checkout_producer')->publish(serialize($msg));

        return $this->redirect(
            $this->generateUrl(
                'project_show',
                array('id' => $id)
            )
        );
    }
}
