<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/27/2019
 * Time: 9:41 PM
 */

namespace Backend\Account;


use Throwable;

class MissingParametersException extends \Exception
{
    public function __construct(array $missingParameters, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(implode(" ", $missingParameters), $code, $previous);
    }
}