<?php

namespace QaSystem\CoreBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        // Menu will be a navbar menu anchored to right
        $menu = $factory->createItem('root', array(
            'navbar' => true,
            'pull-right' => true,
        ));

        $dropdown = $menu->addChild(
            'Actions',
            array(
                'dropdown' => true,
                'caret'    => true,
                'linkAttributes' => array(
                    'data-bypass' => '1'
                ),
            )
        );

        $dropdown->addChild('Projects', array(
            'route' => 'project',
            'linkAttributes' => array(
                'data-bypass' => '1'
            ),
        ));

        $dropdown->addChild('Deployments', array(
            'route' => 'deployment',
            'linkAttributes' => array(
                'data-bypass' => '1'
            ),
        ));

        $dropdown->addChild('Recipes', array(
            'route' => 'recipe',
            'linkAttributes' => array(
                'data-bypass' => '1'
            ),
        ));
        $dropdown->addChild('Time Report', array(
            'route' => 'jira_reporting_worklog',
            'linkAttributes' => array(
                'data-bypass' => '1'
            ),
        ));

        return $menu;
    }
}