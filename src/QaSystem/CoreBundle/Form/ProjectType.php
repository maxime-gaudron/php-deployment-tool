<?php

namespace QaSystem\CoreBundle\Form;

use QaSystem\CoreBundle\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add(
                'type',
                'choice',
                ['choices' => [Project::TYPE_LOCAL_GIT => Project::TYPE_LOCAL_GIT, Project::TYPE_GITHUB => Project::TYPE_GITHUB]]
            )
            ->add('uri', null, ['required' => false])
            ->add('github_username', null, ['required' => false, 'data' => $options['github_username']])
            ->add('github_repository', null, ['required' => false, 'data' => $options['github_repository']])
            ->add('github_token', null, ['required' => false, 'data' => $options['github_token']])
            ->add('variables', 'textarea', ['attr' => ['rows' => 20]]);
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'        => 'QaSystem\CoreBundle\Entity\Project',
                'github_username'   => '',
                'github_repository' => '',
                'github_token'      => '',
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'qasystem_corebundle_project';
    }
}
