<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/8/2019
 * Time: 12:12 AM
 */

namespace BackEnd\Routing\Request\Connection;


use Throwable;

class MissingParametersException extends \Exception
{
    public function __construct(array $missingParameters, string $requestName, int $code = 0, Throwable $previous = null)
    {
        $message = "Missing parameters ".implode(', ', $missingParameters)." in request ".$requestName;
        parent::__construct($message, $code, $previous);
    }
}