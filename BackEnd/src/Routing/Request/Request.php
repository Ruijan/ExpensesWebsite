<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/2/2019
 * Time: 8:58 PM
 */

namespace BackEnd\Routing\Request;

interface Request
{
    public function init();
    public function getResponse();
}