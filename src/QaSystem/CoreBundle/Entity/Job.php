<?php

namespace QaSystem\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Job
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="QaSystem\CoreBundle\Entity\JobRepository")
 */
class Job
{
    const STATUS_PENDING = 'pending';
    const STATUS_RUNNING = 'running';
    const STATUS_DONE = 'done';
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
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="command", type="text")
     */
    private $command;

    /**
     * @var string
     *
     * @ORM\Column(name="output", type="text", nullable=true)
     */
    private $output;

    /**
     * @var string
     *
     * @ORM\Column(name="params", type="json_array", nullable=true)
     */
    private $params;

    public function __construct()
    {
        $this->status = static::STATUS_PENDING;
        $this->setOutput('');
        $this->setParams(array());
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
     * Set status
     *
     * @param string $status
     * 
*@return Job
     */
    public function setStatus($status)
    {
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
*@return Job
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
     * Set params
     *
     * @param array $params
     * 
*@return Job
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set command
     *
     * @param string $command
     * 
*@return Job
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Get command
     *
     * @return string 
     */
    public function getCommand()
    {
        return $this->command;
    }
}
