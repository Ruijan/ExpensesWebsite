<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/2/2019
 * Time: 8:58 PM
 */

namespace BackEnd\Routing\Request;

use BackEnd\Routing\Request\ServerProperties;

abstract class PostRequest implements Request
{
    public function init()
    {
        $post_array = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        foreach ($post_array as $key => $value) {
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