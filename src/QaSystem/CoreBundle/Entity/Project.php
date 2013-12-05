<?php

namespace QaSystem\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GitElephant\Repository;

/**
 * Project
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="QaSystem\CoreBundle\Entity\ProjectRepository")
 */
class Project
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
     * @var string
     *
     * @ORM\Column(name="uri", type="string", length=255)
     */
    private $uri;


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
     * @return Project
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
     * Set uri
     *
     * @param string $uri
     * @return Project
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Get repository
     *
     * @return Repository
     */
    public function getRepository()
    {
        return Repository::open($this->uri);
    }
}
