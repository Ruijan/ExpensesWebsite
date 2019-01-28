<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/28/2019
 * Time: 10:46 PM
 */

namespace BackEnd\Database\DBUsers;


use Throwable;

class UndefinedUserEmail extends \Exception
{
    public function __construct(string $expectedUserEmail = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Couldn't find payee with email " . $expectedUserEmail . " in DBUsers.", $code, $previous);
    }
}