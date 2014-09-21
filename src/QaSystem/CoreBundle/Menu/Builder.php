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
            )
        );

        $dropdown->addChild('Projects', array(
            'route' => 'project',
        ));

        $dropdown->addChild('Deployments', array(
            'route' => 'deployment',
        ));

        $dropdown->addChild('Recipes', array(
            'route' => 'recipe',
        ));

        return $menu;
    }
}