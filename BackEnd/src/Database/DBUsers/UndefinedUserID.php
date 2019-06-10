<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/28/2019
 * Time: 10:30 PM
 */

namespace BackEnd\Database\DBUsers;


use Throwable;

class UndefinedUserID extends \Exception
{
    /**
     * UndefinedUserID constructor.
     * @param $expectedPayerID
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($expectedPayerID, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Couldn't find user with ID " . $expectedPayerID . " in DBUsers.", $code, $previous);
    }
}