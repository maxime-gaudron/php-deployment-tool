<?php

// Creating a fixture set with own configuration,
$set = new h4cc\AliceFixturesBundle\Fixtures\FixtureSet(array(
    'do_drop' => true,
    'do_persist' => true,
));

$set->addFile(__DIR__.'/yml/projects.yml', 'yaml');

return $set;
