<?php

namespace QaSystem\CoreBundle\Twig;

class QaSystemCoreExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('jsonPretty', array($this, 'jsonPrettyFilter')),
        );
    }

    public function jsonPrettyFilter($json)
    {
        return json_encode(json_decode($json, true), JSON_PRETTY_PRINT);
    }

    public function getName()
    {
        return 'qa_system_core_extension';
    }
}
