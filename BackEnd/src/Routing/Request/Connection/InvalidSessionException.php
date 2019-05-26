<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 5/17/2019
 * Time: 8:59 PM
 */

namespace BackEnd\Routing\Request\Connection;

use Throwable;

class InvalidSessionException extends \Exception
{
    public function __construct(string $requestName, int $code = 0, Throwable $previous = null)
    {
        $message = "Invalid user session key during request ".$requestName;
        parent::__construct($message, $code, $previous);
    }
}