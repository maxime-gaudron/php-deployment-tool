<?php

namespace QaSystem\CoreBundle\Form;

use QaSystem\CoreBundle\Entity\Job;
use QaSystem\CoreBundle\Workflow\Engine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Process\Process;

class JobType extends AbstractType
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
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->task['parameters'] as $parameter) {
            $builder
                ->add(
                    $parameter['code'],
                    'choice',
                    array(
                        'attr'    => array('class' => 'chosen-select'),
                        'choices' => $this->getValues($parameter),
                    )
                );
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'qasystem_corebundle_job';
    }

    /**
     * @param array $parameter
     *
     * return array
     */
    public function getValues(array $parameter)
    {
        if ($parameter['type'] === 'choices') {
            return $parameter['values'];
        }
        
        if ($parameter['type'] === 'script') {
            $process = new Process($parameter['script']);
            $process->run();

            return json_decode($process->getOutput(), true);
        }
    }
}
