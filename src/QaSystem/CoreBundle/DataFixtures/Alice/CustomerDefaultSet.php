<?php

// Creating a fixture set with own configuration,
$set = new h4cc\AliceFixturesBundle\Fixtures\FixtureSet(array(
    'do_drop' => false,
    'do_persist' => true,
));

$set->addFile(__DIR__.'/yml/projects.yml', 'yaml');
$set->addFile(__DIR__.'/yml/recipes.yml', 'yaml');
$set->addFile(__DIR__.'/yml/servers.yml', 'yaml');
$set->addFile(__DIR__.'/yml/deployments.yml', 'yaml');

return $set;
