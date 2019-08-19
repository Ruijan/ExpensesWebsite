<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/16/2019
 * Time: 5:17 PM
 */

namespace BackEnd\tests;

use BackEnd\Category;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{

    public function test__construct()
    {
        $category = new Category("food", 5, "2019-06-15 12:00:05");
        $this->assertEquals("food", $category->getName());
        $this->assertEquals(5, $category->getUserID());
        $this->assertEquals("2019-06-15 12:00:05", $category->getAddedDate());
    }
}
