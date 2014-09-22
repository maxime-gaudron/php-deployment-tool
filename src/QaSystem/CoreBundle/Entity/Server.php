<?php

namespace QaSystem\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * Server
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="QaSystem\CoreBundle\Entity\ServerRepository")
 */
class Server
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
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Deployment", mappedBy="server", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $deployments;

    public function __construct()
    {
        $this->deployments = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

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
     * @return Server
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
     * Add deployments
     *
     * @param Deployment $deployments
     *
     * @return Server
     */
    public function addDeployment(Deployment $deployments)
    {
        $this->deployments[] = $deployments;

        return $this;
    }

    /**
     * Remove deployments
     *
     * @param Deployment $deployments
     */
    public function removeDeployment(Deployment $deployments)
    {
        $this->deployments->removeElement($deployments);
    }

    /**
     * Get deployments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDeployments()
    {
        return $this->deployments;
    }

    public function getLastDeploy($status) {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("status", $status))
            ->orderBy(array('endDate' => Criteria::DESC))
            ->setFirstResult(0)
            ->setMaxResults(1)
        ;

        return $this->deployments->matching($criteria)->first();
    }
}
