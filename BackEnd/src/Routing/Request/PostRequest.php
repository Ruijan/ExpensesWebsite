<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/2/2019
 * Time: 8:58 PM
 */

namespace BackEnd\Routing\Request;

use BackEnd\Routing\Request\ServerProperties;

abstract class PostRequest extends HTTPRequest
{
    public function init()
    {
        $post_array = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        foreach ($post_array as $key => $value) {
            $this->{$this->toCamelCase($key)} = $value;
        }
    }
}