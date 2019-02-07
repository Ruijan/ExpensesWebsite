<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/2/2019
 * Time: 9:45 PM
 */

namespace BackEnd\Routing\Request;


use BackEnd\Routing\Request\ServerProperties;

abstract class GetRequest implements Request
{
    public function init()
    {
        $get_array = filter_var_array($_GET, FILTER_SANITIZE_SPECIAL_CHARS);
        foreach($get_array as $key => $value)
        {
            $this->{$this->toCamelCase($key)} = $value;
        }
    }

    protected function toCamelCase($string)
    {
        $result = strtolower($string);

        preg_match_all('/_[a-z]/', $result, $matches);
        foreach ($matches[0] as $match) {
            $c = str_replace('_', '', strtoupper($match));
            $result = str_replace($match, $c, $result);
        }
        return $result;
    }
}