<?php

namespace Blixt\Persistence\Drivers;

use Blixt\Persistence\Entities\Column;
use Blixt\Persistence\Entities\Document;
use Blixt\Persistence\Entities\Entity;
use Blixt\Persistence\Entities\Field;
use Blixt\Persistence\Entities\Occurrence;
use Blixt\Persistence\Entities\Position;
use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\Entities\Term;
use Blixt\Persistence\Entities\Word;

abstract class AbstractDriver
{
    protected $entities = [
        Column::class,
        Document::class,
        Field::class,
        Occurrence::class,
        Position::class,
        Schema::class,
        Term::class,
        Word::class
    ];

    protected function getTableFromEntityClassName(string $className): string
    {
        if ($table = constant($className . '::TABLE')) {
            return $table;
        }

        return $className;
    }

    protected function getTableFromEntity(Entity $entity): string
    {
        return $this->getTableFromEntityClassName(get_class($entity));
    }
}