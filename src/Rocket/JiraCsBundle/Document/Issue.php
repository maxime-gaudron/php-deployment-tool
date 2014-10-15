<?php

namespace Rocket\JiraCsBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(repositoryClass="Rocket\JiraCsBundle\Document\IssueRepository")
 */
class Issue
{
    /**
     * @MongoDB\Id
     */
    protected $issueId;

    /**
     * @MongoDB\Hash
     */
    protected $expand;

    /**
     * @MongoDB\String
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $self;

    /**
     * @MongoDB\String
     */
    protected $key;
    
    /**
     * @MongoDB\date
     */
    protected $updatedAt;

    /**
     * @MongoDB\Hash
     */
    protected $fields;

    /**
     * @MongoDB\Hash
     */
    protected $expandedInformation;

    /**
     * @MongoDB\collection
     */
    protected $worklogs;

    /**
     * Get issueId
     *
     * @return string $issueId
     */
    public function getIssueId()
    {
        return $this->issueId;
    }

    /**
     * Set expand
     *
     * @param array $expand
     * @return self
     */
    public function setExpand(array $expand)
    {
        $this->expand = $expand;
        return $this;
    }

    /**
     * Get expand
     *
     * @return array $expand
     */
    public function getExpand()
    {
        return $this->expand;
    }

    /**
     * Set id
     *
     * @param string $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return string $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set self
     *
     * @param string $self
     * @return self
     */
    public function setSelf($self)
    {
        $this->self = $self;
        return $this;
    }

    /**
     * Get self
     *
     * @return string $self
     */
    public function getSelf()
    {
        return $this->self;
    }

    /**
     * Set key
     *
     * @param string $key
     * @return self
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Get key
     *
     * @return string $key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set fields
     *
     * @param array $fields
     * @return self
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * Get fields
     *
     * @return array $fields
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set expandedInformation
     *
     * @param array $expandedInformation
     * @return self
     */
    public function setExpandedInformation(array $expandedInformation)
    {
        $this->expandedInformation = $expandedInformation;
        return $this;
    }

    /**
     * Get expandedInformation
     *
     * @return array $expandedInformation
     */
    public function getExpandedInformation()
    {
        return $this->expandedInformation;
    }

    /**
     * Set updatedAt
     *
     * @param \MongoDate $updatedAt
     * @return self
     */
    public function setUpdatedAt(\MongoDate $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set worklogs
     *
     * @param array $worklogs
     * @return self
     */
    public function setWorklogs(array $worklogs)
    {
        $this->worklogs = $worklogs;
        return $this;
    }

    /**
     * Get worklogs
     *
     * @return array $worklogs
     */
    public function getWorklogs()
    {
        return $this->worklogs;
    }
}
