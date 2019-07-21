<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/21/2019
 * Time: 10:45 AM
 */

namespace BackEnd\Database\DBPayees;
use Throwable;

class UndefinedPayeeException extends \Exception
{
    public function __construct(string $payeeID, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Couldn't find payee with ID " . $payeeID . " in DBPayees.", $code, $previous);
    }
}