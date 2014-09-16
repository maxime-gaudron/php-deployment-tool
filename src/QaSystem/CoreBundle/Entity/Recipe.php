<?php

namespace QaSystem\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Recipe
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="QaSystem\CoreBundle\Entity\RecipeRepository")
 */
class Recipe
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(name="workflow", type="json_array")
     */
    private $workflow;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Recipe
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set workflow
     *
     * @param string $workflow
     * @return Recipe
     */
    public function setWorkflow($workflow)
    {
        $this->workflow = $workflow;

        return $this;
    }

    /**
     * Get workflow
     *
     * @return string
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        $jsonEncoder = new JsonEncoder();
        try {
            $jsonEncoder->decode($this->workflow, JsonEncoder::FORMAT);
        } catch (UnexpectedValueException $e) {
            $context->buildViolation('Javascript malformed: "%error_message%"')
                ->setParameter('%error_message%', $e->getMessage())
                ->addViolation();
        }
    }
}
