<?php

namespace Blixt\Search\Results;

interface Result
{
    public function getKey();
    public function getScore();
    public function getSchema();
}
