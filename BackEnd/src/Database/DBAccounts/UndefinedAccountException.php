<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/9/2019
 * Time: 12:06 PM
 */

namespace BackEnd\Database\DBAccounts;

use Throwable;

class UndefinedAccountException extends \Exception
{
    /**
     * UndefinedUserID constructor.
     * @param $expectedName
     * @param $expectedUser
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($expectedName, $expectedUser, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Couldn't find account with Name " . $expectedName . " and user ".$expectedUser." in DBAccounts.", $code, $previous);
    }

}