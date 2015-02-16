<?php

namespace QaSystem\CoreBundle\Controller;

use QaSystem\CoreBundle\Entity\DeploymentRepository;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use QaSystem\CoreBundle\Form\DeploymentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use QaSystem\CoreBundle\Entity\Deployment;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
//        $em = $this->getDoctrine()->getManager();
//
//        $status = $this->get('request')->query->get('status', 'all');
//
//        /** @var DeploymentRepository $repo */
//        $repo = $em->getRepository('QaSystemCoreBundle:Deployment');
//        $query = $repo->createQueryBuilderForPagination($status)
//            ->getQuery();
//
//        $paginator  = $this->get('knp_paginator');
//        $entities = $paginator->paginate(
//            $query,
//            $this->get('request')->query->get('page', 1),
//            10
//        );

        return array(
            'tasks' => $this->container->getParameter('tasks'),
//            'entities' => $entities,
//            'selectedStatus' => $status,
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
        $deployment = new Deployment();
        $form = $this->createCreateForm($deployment);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');

            $em->getConnection()->beginTransaction();

            try {
                $em->persist($deployment);
                $em->flush();

                $msg = array(
                    'deploymentId' => $deployment->getId()
                );

                $this->get('old_sound_rabbit_mq.project_deploy_producer')->publish(serialize($msg));

                $em->getConnection()->commit();
            } catch (\Exception $e) {
                $em->getConnection()->rollback();

                throw $e;
            }

            return $this->redirect($this->generateUrl('deployment_show', array('id' => $deployment->getId())));
        }

        return array(
            'entity' => $deployment,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Deployment entity.
     *
     * @param array $task
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(array $task)
    {
        $form = $this->createForm(
            new DeploymentType($task),
            null,
            array(
                'action' => $this->generateUrl('deployment_create'),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Deployment entity.
     *
     * @Route("/new/{taskName}", name="deployment_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($taskName)
    {
         $tasks = $this->container->getParameter('tasks');

        if (!array_key_exists($taskName, $tasks)) {
            throw $this->createNotFoundException();
        }

        $deployment = new Deployment();
        $form = $this->createCreateForm($tasks[$taskName]);

        return array(
            'entity' => $deployment,
            'form' => $form->createView(),
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
            'entity' => $entity,
        );
    }
}
