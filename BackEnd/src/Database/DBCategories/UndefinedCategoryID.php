<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/8/2019
 * Time: 10:16 PM
 */

namespace BackEnd\Database\DBCategories;

use Throwable;

class UndefinedCategoryID extends \Exception
{
    public function __construct(string $expectedSubCategoryID, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Couldn't find category with id " . $expectedSubCategoryID . " in DBCategories.", $code, $previous);
    }
}