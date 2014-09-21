<?php

namespace QaSystem\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Deployment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="QaSystem\CoreBundle\Entity\DeploymentRepository")
 */
class Deployment
{
    const STATUS_PENDING = 'pending';
    const STATUS_DEPLOYING = 'deploying';
    const STATUS_DEPLOYED = 'deployed';
    const STATUS_ERROR = 'error';
    const STATUS_ABORTED = 'aborted';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetimetz", nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetimetz", nullable=true)
     */
    private $endDate;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="branch", type="string")
     */
    private $branch;

    /**
     * @var integer
     *
     * @ORM\Column(name="commits_behind", type="integer", nullable=true)
     */
    private $commitsBehind;

    /**
     * @var string
     *
     * @ORM\Column(name="output", type="text", nullable=true)
     */
    private $output;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project")
     */
    private $project;

    /**
     * @var Recipe
     *
     * @ORM\ManyToOne(targetEntity="Recipe")
     */
    private $recipe;

    /**
     * @var Server
     *
     * @ORM\ManyToOne(targetEntity="Server", inversedBy="deployments")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $server;

    function __construct()
    {
        $this->status = static::STATUS_PENDING;
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
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Deployment
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Deployment
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set project
     *
     * @param Project $project
     *
     * @return Deployment
     */
    public function setProject(Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set recipe
     *
     * @param Recipe $recipe
     *
     * @return Deployment
     */
    public function setRecipe(Recipe $recipe = null)
    {
        $this->recipe = $recipe;

        return $this;
    }

    /**
     * Get recipe
     *
     * @return Recipe
     */
    public function getRecipe()
    {
        return $this->recipe;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @throws \InvalidArgumentException
     * @return Deployment
     */
    public function setStatus($status)
    {
        $validStatus = array(
            self::STATUS_PENDING,
            self::STATUS_DEPLOYING,
            self::STATUS_DEPLOYED,
            self::STATUS_ERROR,
            self::STATUS_ABORTED,
        );

        if (!in_array($status, $validStatus)) {
            throw new \InvalidArgumentException("Invalid status");
        }

        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set output
     *
     * @param string $output
     *
     * @return Deployment
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Get output
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Set branch
     *
     * @param string $branch
     *
     * @return Deployment
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * Get branch
     *
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * Set server
     *
     * @param Server $server
     *
     * @return Deployment
     */
    public function setServer(Server $server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Get server
     *
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Set commitsBehind
     *
     * @param integer $commitsBehind
     *
     * @return Deployment
     */
    public function setCommitsBehind($commitsBehind)
    {
        $this->commitsBehind = $commitsBehind;

        return $this;
    }

    /**
     * Get commitsBehind
     *
     * @return integer
     */
    public function getCommitsBehind()
    {
        return $this->commitsBehind;
    }
}