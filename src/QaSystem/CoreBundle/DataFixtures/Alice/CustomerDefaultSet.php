<?php

// Creating a fixture set with own configuration,
$set = new h4cc\AliceFixturesBundle\Fixtures\FixtureSet(array(
    'do_drop' => false,
    'do_persist' => true,
));

$set->addFile(__DIR__.'/yml/jobs.yml', 'yaml');

return $set;
