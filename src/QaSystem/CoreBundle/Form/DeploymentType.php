<?php

namespace QaSystem\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DeploymentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('project', 'entity', array(
                'class' => 'QaSystemCoreBundle:Project',
                'property' => 'name',
            ))
            ->add('recipe', 'entity', array(
                'class' => 'QaSystemCoreBundle:Recipe',
                'property' => 'name',
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'QaSystem\CoreBundle\Entity\Deployment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'qasystem_corebundle_deployment';
    }
}
