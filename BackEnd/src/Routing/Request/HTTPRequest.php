<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/12/2019
 * Time: 11:11 PM
 */

namespace BackEnd\Routing\Request;
use BackEnd\Routing\Request\Request;

abstract class HTTPRequest implements Request
{
    public function toCamelCase($string)
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