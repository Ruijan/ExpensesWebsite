<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/11/2019
 * Time: 9:27 PM
 */

namespace BackEnd\Database\DBCurrencies;

use Throwable;

class UndefinedCurrencyException extends \Exception
{
    /**
     * UndefinedCurrencyException constructor.
     * @param $expectedName
     * @param $expectedShortName
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($expectedName, $expectedShortName, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Couldn't find currency with Name " . $expectedName . " (".$expectedShortName.") in DBCurrencies.", $code, $previous);
    }
}