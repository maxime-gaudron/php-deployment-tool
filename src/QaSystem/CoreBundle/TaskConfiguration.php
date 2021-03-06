<?php

namespace QaSystem\CoreBundle;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class TaskConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('qa_task_list');

        $rootNode->children()
            ->arrayNode('tasks')
                ->isRequired()
                ->prototype('array')
                    ->children()
                        ->scalarNode('name')->isRequired()->end()
                        ->scalarNode('command')->isRequired()->end()
                    ->arrayNode('parameters')
                        ->prototype('array')
                            ->validate()
                                ->ifTrue(function($v){ return $v['type'] === 'choice' && count($v['values']) === 0;})
                                ->thenInvalid('Missing values for "array" parameter type.')
                                ->end()
                            ->validate()
                                ->ifTrue(function($v){ return $v['type'] === 'script' && !array_key_exists('script', $v);})
                                ->thenInvalid('Missing values for "array" parameter type.')
                                ->end()
                            ->children()
                                ->scalarNode('name')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('code')
                                    ->isRequired()
                                    ->end()
                                ->scalarNode('script')
                                    ->cannotBeEmpty()
                                    ->end()
                                ->scalarNode('type')
                                    ->isRequired()
                                    ->validate()
                                        ->ifNotInArray(array('choice', 'script'))
                                        ->thenInvalid('Invalid parameter type "%s"')
                                        ->end()
                                ->end()
                                ->arrayNode('values')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
