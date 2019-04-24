<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/2/2019
 * Time: 10:24 PM
 */

namespace BackEnd\Routing;

class ServerProperties
{
    private $requestUri;
    private $documentRoot;
    public function __construct(){
        foreach($_SERVER as $key => $value)
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

    public function getURI(){
        return $this->requestUri;
    }

    public function getDocumentRoot(){
        return $this->documentRoot;
    }

    public function getCurrentFolder(){
        return getcwd();
    }
}