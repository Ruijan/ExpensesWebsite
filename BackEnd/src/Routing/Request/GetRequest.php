<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/2/2019
 * Time: 9:45 PM
 */

namespace BackEnd\Routing\Request;


use BackEnd\Routing\Request\ServerProperties;

abstract class GetRequest extends HTTPRequest
{
    public function init()
    {
        $get_array = filter_var_array($_GET, FILTER_SANITIZE_SPECIAL_CHARS);
        foreach ($get_array as $key => $value) {
            $this->{$this->toCamelCase($key)} = $value;
        }
    }
}