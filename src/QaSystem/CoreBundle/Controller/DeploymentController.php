<?php

namespace QaSystem\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use QaSystem\CoreBundle\Form\DeploymentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use QaSystem\CoreBundle\Entity\Deployment;

/**
 * Deployment controller.
 *
 * @Route("/deployment")
 */
class DeploymentController extends Controller
{

    /**
     * Lists all Deployment entities.
     *
     * @Route("/", name="deployment")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('QaSystemCoreBundle:Deployment')->findBy([], ['id' => 'desc']);

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Deployment entity.
     *
     * @Route("/", name="deployment_create")
     * @Method("POST")
     * @Template("QaSystemCoreBundle:Deployment:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Deployment();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('deployment_deploy', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Deployment entity.
     *
     * @param Deployment $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Deployment $entity)
    {
        $form = $this->createForm(new DeploymentType(), $entity, array(
            'action' => $this->generateUrl('deployment_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Recipe entity.
     *
     * @Route("/new", name="deployment_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Deployment();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Deployment entity.
     *
     * @Route("/{id}", name="deployment_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QaSystemCoreBundle:Deployment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Deployment entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Trigger a deployment.
     *
     * @Route("/deploy/{id}", name="deployment_deploy")
     * @Method("GET")
     * @Template()
     */
    public function deployAction($id)
    {
        $msg = array(
            'deploymentId' => $id
        );

        $this->get('old_sound_rabbit_mq.project_deploy_producer')->publish(serialize($msg));

        return $this->redirect($this->generateUrl('deployment_show', array('id' => $id)));
    }
}
