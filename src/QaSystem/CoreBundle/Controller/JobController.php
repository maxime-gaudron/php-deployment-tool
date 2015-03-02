<?php

namespace QaSystem\CoreBundle\Controller;

use QaSystem\CoreBundle\Entity\JobRepository;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use QaSystem\CoreBundle\Form\JobType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use QaSystem\CoreBundle\Entity\Job;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Job controller.
 *
 * @Route("/deployment")
 */
class JobController extends Controller
{

    /**
     * Lists all Job entities.
     *
     * @Route("/", name="deployment")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $status = $this->get('request')->query->get('status', 'all');
        /** @var JobRepository $repo */
        $repo = $em->getRepository('QaSystemCoreBundle:Job');
        $query = $repo->createQueryBuilderForPagination($status)
            ->getQuery();
        $paginator  = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            10
        );
        return array(
            'tasks' => $this->container->getParameter('tasks'),
            'entities' => $entities,
            'selectedStatus' => $status,
        );
    }

    /**
     * Creates a new Job entity.
     *
     * @Route("/new/{taskName}", name="deployment_create")
     * @Method("POST")
     * @Template("QaSystemCoreBundle:Job:new.html.twig")
     */
    public function createAction($taskName, Request $request)
    {
        $job = new Job();
        $form = $this->createCreateForm($taskName);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');

            $task = $this->getTaskByName($taskName);
            $job->setCommand($task['name']);
            
            $em->persist($job);
            $em->flush();

            return $this->redirect($this->generateUrl('deployment_show', array('id' => $job->getId())));
        }

        return array(
            'entity' => $job,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Job entity.
     *
     * @param string $taskName
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($taskName)
    {
        $type = new JobType(
            $this->getTaskByName($taskName)
        );
        
        $form = $this->createForm(
            $type,
            null,
            array(
                'action' => $this->generateUrl('deployment_create', array('taskName' => $taskName)),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Job entity.
     *
     * @Route("/new/{taskName}", name="deployment_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($taskName)
    {
        $form = $this->createCreateForm($taskName);

        return array(
            'entity' => new Job(),
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Job entity.
     *
     * @Route("/{id}", name="deployment_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QaSystemCoreBundle:Job')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * @param $taskName
     *
     * @return mixed
     */
    protected function getTaskByName($taskName)
    {
        $tasks = $this->container->getParameter('tasks');

        if (!array_key_exists($taskName, $tasks)) {
            throw $this->createNotFoundException();
        }

        $task = $tasks[$taskName];

        return $task;
    }
}
