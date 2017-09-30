<?php

namespace Blixt\Storage;

interface BuilderInterface
{
    /**
     * Build a storage driver.
     *
     * @return \Blixt\Storage\Drivers\DriverInterface
     */
    public function build();
}