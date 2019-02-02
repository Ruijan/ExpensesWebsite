<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/2/2019
 * Time: 4:58 PM
 */

namespace Loader;

use BackEnd\Loader\CSVLoader;
use PHPUnit\Framework\TestCase;
use BackEnd\Loader\LoaderFactory;

class LoaderFactoryTest extends TestCase
{
    public function testCreateLoader()
    {
        $factory = new LoaderFactory();
        $loader = $factory->createLoader("CSVLoader");
        $this->assertEquals(CSVLoader::class, get_class($loader));
    }

    public function testCreateLoaderWithWrongTpeShouldThrow()
    {
        $wrongType = "test";
        $factory = new LoaderFactory();
        $this->expectException(\InvalidArgumentException::class);
        $factory->createLoader($wrongType);
    }
}
