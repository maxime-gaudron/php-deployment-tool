<?php

namespace Rocket\JiraCsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Rocket\JiraCsBundle\Document\Project;
use Rocket\JiraCsBundle\Form\ProjectType;

/**
 * Project controller.
 *
 * @Route("/jira/project")
 */
class ProjectController extends Controller
{
    /**
     * Lists all Project documents.
     *
     * @Route("/", name="jira_project")
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        $dm = $this->getDocumentManager();

        $documents = $dm->getRepository('RocketJiraCsBundle:Project')->findAll();

        return array('documents' => $documents);
    }

    /**
     * Displays a form to create a new Project document.
     *
     * @Route("/new", name="jira_project_new")
     * @Template()
     *
     * @return array
     */
    public function newAction()
    {
        $document = new Project();
        $form = $this->createForm(new ProjectType(), $document);

        return array(
            'document' => $document,
            'form'     => $form->createView()
        );
    }

    /**
     * Creates a new Project document.
     *
     * @Route("/create", name="jira_project_create")
     * @Method("POST")
     * @Template("RocketJiraCsBundle:Project:new.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $document = new Project();
        $form     = $this->createForm(new ProjectType(), $document);
        $form->bind($request);

        if ($form->isValid()) {
            $dm = $this->getDocumentManager();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('jira_project_show', array('id' => $document->getId())));
        }

        return array(
            'document' => $document,
            'form'     => $form->createView()
        );
    }

    /**
     * Finds and displays a Project document.
     *
     * @Route("/{id}/show", name="jira_project_show")
     * @Template()
     *
     * @param string $id The document ID
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function showAction($id)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('RocketJiraCsBundle:Project')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Project document.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'document' => $document,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Project document.
     *
     * @Route("/{id}/edit", name="jira_project_edit")
     * @Template()
     *
     * @param string $id The document ID
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function editAction($id)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('RocketJiraCsBundle:Project')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Project document.');
        }

        $editForm = $this->createForm(new ProjectType(), $document);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Project document.
     *
     * @Route("/{id}/update", name="jira_project_update")
     * @Method("POST")
     * @Template("RocketJiraCsBundle:Project:edit.html.twig")
     *
     * @param Request $request The request object
     * @param string $id       The document ID
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function updateAction(Request $request, $id)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('RocketJiraCsBundle:Project')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Project document.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm   = $this->createForm(new ProjectType(), $document);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('jira_project_edit', array('id' => $id)));
        }

        return array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Project document.
     *
     * @Route("/{id}/delete", name="jira_project_delete")
     * @Method("POST")
     *
     * @param Request $request The request object
     * @param string $id       The document ID
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $dm = $this->getDocumentManager();
            $document = $dm->getRepository('RocketJiraCsBundle:Project')->find($id);

            if (!$document) {
                throw $this->createNotFoundException('Unable to find Project document.');
            }

            $dm->remove($document);
            $dm->flush();
        }

        return $this->redirect($this->generateUrl('jira_project'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * Returns the DocumentManager
     *
     * @return DocumentManager
     */
    private function getDocumentManager()
    {
        return $this->get('doctrine.odm.mongodb.document_manager');
    }
}
