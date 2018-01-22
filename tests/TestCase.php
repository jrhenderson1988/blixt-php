<?php

namespace BlixtTests;

use Faker\Factory;
use Mockery;
use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * TestCase constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->faker = Factory::create();
    }

    public function tearDown()
    {
        Mockery::close();
    }
}