<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/9/2019
 * Time: 5:06 PM
 */

namespace BackEnd\Routing\Request\Connection;

use Throwable;

class InvalidCredentialsException extends \Exception
{
    public function __construct(string $requestName, int $code = 0, Throwable $previous = null)
    {
        $message = "Invalid user credentials during request ".$requestName;
        parent::__construct($message, $code, $previous);
    }
}