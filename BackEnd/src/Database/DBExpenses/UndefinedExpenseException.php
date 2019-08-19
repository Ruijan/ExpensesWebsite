<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/24/2019
 * Time: 7:55 PM
 */

namespace BackEnd\Database\DBExpenses;
use Throwable;

class UndefinedExpenseException extends \Exception
{
    public function __construct(string $expenseID, int $code = 0, Throwable $previous = null)
{
    parent::__construct("Couldn't find expense with ID " . $expenseID . " in DBExpense.", $code, $previous);
}
}