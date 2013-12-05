<?php

namespace QaSystem\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use QaSystem\CoreBundle\Entity\Step;
use QaSystem\CoreBundle\Form\StepType;

/**
 * Step controller.
 *
 * @Route("/step")
 */
class StepController extends Controller
{

    /**
     * Lists all Step entities.
     *
     * @Route("/", name="step")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('QaSystemCoreBundle:Step')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Step entity.
     *
     * @Route("/", name="step_create")
     * @Method("POST")
     * @Template("QaSystemCoreBundle:Step:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Step();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('step_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Step entity.
    *
    * @param Step $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Step $entity)
    {
        $form = $this->createForm(new StepType(), $entity, array(
            'action' => $this->generateUrl('step_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Step entity.
     *
     * @Route("/new", name="step_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Step();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Step entity.
     *
     * @Route("/{id}", name="step_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QaSystemCoreBundle:Step')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Step entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Step entity.
     *
     * @Route("/{id}/edit", name="step_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QaSystemCoreBundle:Step')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Step entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Step entity.
    *
    * @param Step $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Step $entity)
    {
        $form = $this->createForm(new StepType(), $entity, array(
            'action' => $this->generateUrl('step_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Step entity.
     *
     * @Route("/{id}", name="step_update")
     * @Method("PUT")
     * @Template("QaSystemCoreBundle:Step:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QaSystemCoreBundle:Step')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Step entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('step_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Step entity.
     *
     * @Route("/{id}", name="step_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('QaSystemCoreBundle:Step')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Step entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('step'));
    }

    /**
     * Creates a form to delete a Step entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('step_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
