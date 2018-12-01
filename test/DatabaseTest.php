<?php

/**
 *  test case.
 */
use PHPUnit\Framework\TestCase;
use src\Database;

class DatabaseTest extends TestCase
{

    private $dBHandler;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->dBHandler = Database::create();
        // TODO Auto-generated DatabaseTest::setUp()
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated DatabaseTest::tearDown()
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct()
    {
        // TODO Auto-generated constructor
    }
}

