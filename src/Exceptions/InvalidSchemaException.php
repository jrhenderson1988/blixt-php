<?php

namespace Blixt\Exceptions;

class InvalidSchemaException extends BlixtException
{
    /**
     * @return \Blixt\Exceptions\InvalidSchemaException
     */
    public static function noColumns()
    {
        return new static("The provided schema has no columns.");
    }
}