<?php

namespace Blixt\Index\Schema;

use Illuminate\Support\Collection;
use InvalidArgumentException;

class Blueprint
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $definitions;

    /**
     * Schema constructor.
     *
     * @param string|mixed                         $name
     * @param \Illuminate\Support\Collection|array $definitions
     */
    public function __construct($name, $definitions = null)
    {
        $this->setName($name);
        $this->setDefinitions(! is_null($definitions) ? $definitions : new Collection());
    }

    /**
     * Set the name of the blueprint.
     *
     * @param string|mixed $name
     */
    public function setName($name)
    {
        $this->name = strval($name);
    }

    /**
     * Get the name of the blueprint.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the definitions to the given array or collection.
     *
     * @param \Illuminate\Support\Collection|array $definitions
     *
     * @throws \InvalidArgumentException
     */
    public function setDefinitions($definitions)
    {
        if (! $definitions instanceof Collection && ! is_array($definitions)) {
            throw new InvalidArgumentException(
                "The definitions provided must be an array or a collection."
            );
        }

        $this->definitions = new Collection();

        (new Collection($definitions))->each(function ($definition) {
            $this->addDefinition($definition);
        });
    }

    /**
     * Add a definition to the schema blueprint.
     *
     * @param \Blixt\Index\Schema\Definition|string $name
     * @param bool                                  $isIndexed
     * @param bool                                  $isStored
     */
    public function addDefinition($name, $isIndexed = true, $isStored = false)
    {
        $definition = $this->createDefinition($name, $isIndexed, $isStored);

        $this->definitions->put(
            $definition->getName(), $definition
        );
    }

    /**
     * Get the definitions.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * Create a definition from the given data. If the first parameter is already a definiton, it is returned as is.
     *
     * @param \Blixt\Index\Schema\Definition|string $name
     * @param bool                                  $isIndexed
     * @param bool                                  $isStored
     *
     * @return \Blixt\Index\Schema\Definition
     */
    protected function createDefinition($name, $isIndexed = true, $isStored = false)
    {
        if ($name instanceof Definition) {
            return $name;
        }

        if (! is_string($name) || empty($name)) {
            throw new InvalidArgumentException(
                'Invalid definition name, expected non-empty string.'
            );
        }

        return new Definition($name, $isIndexed, $isStored);
    }
}