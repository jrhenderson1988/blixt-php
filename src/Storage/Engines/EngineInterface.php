<?php

namespace Blixt\Storage\Engines;

interface EngineInterface
{
    public function exists();
    public function create();
}