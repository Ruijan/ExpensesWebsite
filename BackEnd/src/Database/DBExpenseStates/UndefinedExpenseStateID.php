<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/8/2019
 * Time: 10:16 PM
 */

namespace BackEnd\Database\DBExpenseStates;

use Throwable;

class UndefinedExpenseStateID extends \Exception
{
    public function __construct(int $expectedExpenseStateID, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Couldn't find expense state with id " . $expectedExpenseStateID . " in DBExpenseState.", $code, $previous);
    }
}