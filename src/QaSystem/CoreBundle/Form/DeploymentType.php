<?php

namespace QaSystem\CoreBundle\Form;

use QaSystem\CoreBundle\Entity\Deployment;
use QaSystem\CoreBundle\Service\VersionControlService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DeploymentType extends AbstractType
{
    /**
     * @var array the definition of the task
     */
    private $task;

    /**
     * @param array $task
     */
    public function __construct(array $task)
    {
        $this->task = $task;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $builder
//            ->add('project', 'entity', array(
//                'class' => 'QaSystemCoreBundle:Project',
//                'property' => 'name',
//                'read_only' => true,
//                'attr' => array('class' => 'chosen-select'),
//                ))
//            ->add('recipe', 'entity', array(
//                'class' => 'QaSystemCoreBundle:Recipe',
//                'property' => 'name',
//                'attr' => array('class' => 'chosen-select'),
//                ))
//            ->add('server', null, array(
//                    'attr' => array('class' => 'chosen-select'),
//                ))
//        ;

//        $versionControlService = $this->versionControlService;
//
//        $formModifier = function (FormInterface $form, Project $project) use ($versionControlService) {
//            $branches = [];
//
//            $gitBranches = $versionControlService->getBranches($project);
//            foreach ($gitBranches as $branch) {
//                $branchName = $branch->getName();
//                $branches[$branchName] = $branchName;
//            }
//
//            $form->add('branch', 'choice', array(
//                    'choices' => $branches,
//                    'attr' => array('class' => 'chosen-select'),
//                ));
//        };
//
//        $builder->addEventListener(
//            FormEvents::PRE_SET_DATA,
//            function (FormEvent $event) use ($formModifier) {
//                /** @var Deployment $deployment */
//                $deployment = $event->getData();
//
//                $project = $deployment->getProject();
//                if ($project) {
//                    $formModifier($event->getForm(), $project);
//                }
//            }
//        );
//
//        $builder->get('project')->addEventListener(
//            FormEvents::POST_SUBMIT,
//            function (FormEvent $event) use ($formModifier) {
//                $project = $event->getForm()->getData();
//
//                $formModifier($event->getForm()->getParent(), $project);
//            }
//        );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
//        $resolver->setDefaults(array(
//            'data_class' => 'QaSystem\CoreBundle\Entity\Deployment'
//        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'qasystem_corebundle_deployment';
    }
}
