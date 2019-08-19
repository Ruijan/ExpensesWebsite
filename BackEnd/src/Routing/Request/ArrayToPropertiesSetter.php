<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/9/2019
 * Time: 2:32 PM
 */

namespace BackEnd\Routing\Request;


class ArrayToPropertiesSetter
{
    public function __construct($array)
    {
        if(!empty($array)){
            foreach ($array as $key => $value) {
                $this->{$this->toCamelCase($key)} = $value;
            }
        }
    }

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

    public function getMissingFields($mandatoryFields)
    {
        $missingFields = array();
        foreach ($mandatoryFields as $key) {
            if ($this->{$this->toCamelCase($key)} == "") {
                $missingFields[] = $key;
            }
        }
        return $missingFields;
    }
}