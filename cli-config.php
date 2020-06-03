<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Odango\Transmission\Container;

$container = Container::get();
$em = $container->get(EntityManager::class);
return ConsoleRunner::createHelperSet($em);