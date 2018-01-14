<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;

try {
    $entityManager = EntityManager::create(
        ['url' => 'sqlite:///:memory:'],
        Setup::createAnnotationMetadataConfiguration([__DIR__ . '/src/Storage/Entities'])
    );

    return ConsoleRunner::createHelperSet($entityManager);
} catch (ORMException $e) {
    echo $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}