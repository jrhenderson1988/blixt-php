<?php

namespace Blixt\Index\Blueprint;

use Illuminate\Support\Collection;

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
     * Blueprint constructor.
     *
     * @param string                              $name
     * @param \Illuminate\Support\Collection|null $definitions
     */
    public function __construct(string $name, ?Collection $definitions = null)
    {
        $this->setName($name);
        $this->setDefinitions(! is_null($definitions) ? $definitions : new Collection());
    }

    /**
     * Set the name of the blueprint.
     *
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get the name of the blueprint.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the definitions to the given array or collection.
     *
     * @param \Illuminate\Support\Collection|array $definitions
     */
    public function setDefinitions(Collection $definitions): void
    {
        $this->definitions = new Collection();

        $definitions->each(function ($definition) {
            $this->addDefinition($definition);
        });
    }

    /**
     * Add a definition to the blueprint.
     *
     * @param \Blixt\Index\Blueprint\Definition $definition
     */
    public function addDefinition(Definition $definition): void
    {
        $this->definitions->put(
            $definition->getName(), $definition
        );
    }

    /**
     * Create a definition from the given information and add it to the blueprint.
     *
     * @param string $name
     * @param bool   $isIndexed
     * @param bool   $isStored
     */
    public function createDefinition(string $name, bool $isIndexed = true, bool $isStored = false): void
    {
        $this->addDefinition(
            new Definition($name, $isIndexed, $isStored)
        );
    }

    /**
     * Get the definitions.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDefinitions(): Collection
    {
        return $this->definitions;
    }
}