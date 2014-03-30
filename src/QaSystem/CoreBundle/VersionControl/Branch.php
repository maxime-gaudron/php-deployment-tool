<?php

namespace QaSystem\CoreBundle\VersionControl;

class Branch
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $hash;

    public function __construct($name, $hash)
    {
        $this->hash = $hash;
        $this->name = $name;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
