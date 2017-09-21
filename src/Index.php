<?php

namespace Blixt;

class Index
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;

        $this->install();
    }

    protected function install()
    {

    }
}