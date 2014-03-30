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
    const TYPE_LOCAL_GIT = 'local_git';

    const TYPE_GITHUB = 'github';

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
     * @var array
     *
     * @ORM\Column(name="variables", type="json_array")
     */
    private $variables;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="github_username", type="string", length=255, nullable=true)
     */
    private $githubUsername;

    /**
     * @var string
     *
     * @ORM\Column(name="github_repository", type="string", length=255, nullable=true)
     */
    private $githubRepository;

    /**
     * @var string
     *
     * @ORM\Column(name="github_token", type="string", length=255, nullable=true)
     */
    private $githubToken;

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
     *
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

    /**
     * Set variables
     *
     * @param array $variables
     * @return Project
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;

        return $this;
    }

    /**
     * Get variables
     *
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }


    /**
     * Set type
     *
     * @param string $type
     * @throws \InvalidArgumentException
     *
     * @return Project
     */
    public function setType($type)
    {
        if (!in_array($type, array(self::TYPE_LOCAL_GIT, self::TYPE_GITHUB))) {
            throw new \InvalidArgumentException("Invalid status");
        }

        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set githubUsername
     *
     * @param string $githubUsername
     * @return Project
     */
    public function setGithubUsername($githubUsername)
    {
        $this->githubUsername = $githubUsername;
    
        return $this;
    }

    /**
     * Get githubUsername
     *
     * @return string 
     */
    public function getGithubUsername()
    {
        return $this->githubUsername;
    }

    /**
     * Set githubRepository
     *
     * @param string $githubRepository
     * @return Project
     */
    public function setGithubRepository($githubRepository)
    {
        $this->githubRepository = $githubRepository;
    
        return $this;
    }

    /**
     * Get githubRepository
     *
     * @return string 
     */
    public function getGithubRepository()
    {
        return $this->githubRepository;
    }

    /**
     * Set githubToken
     *
     * @param string $githubToken
     * @return Project
     */
    public function setGithubToken($githubToken)
    {
        $this->githubToken = $githubToken;
    
        return $this;
    }

    /**
     * Get githubToken
     *
     * @return string 
     */
    public function getGithubToken()
    {
        return $this->githubToken;
    }
}
