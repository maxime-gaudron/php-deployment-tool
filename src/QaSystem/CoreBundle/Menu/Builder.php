<?php

namespace QaSystem\CoreBundle\Menu;

use Knp\Menu\FactoryInterface;

class Builder
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        // Menu will be a navbar menu anchored to right
        $menu = $factory->createItem('root', array(
            'navbar' => true,
            'pull-right' => true,
        ));

        $menu->addChild('Homepage', array(
            'route' => 'homepage',
        ));


        $menu->addChild('Projects', array(
            'route' => 'project',
        ));

        $menu->addChild('Deployments', array(
            'route' => 'deployment',
        ));

        $menu->addChild('Recipes', array(
            'route' => 'recipe',
        ));

        $menu->addChild('Steps', array(
            'route' => 'step',
        ));

        return $menu;
    }
}