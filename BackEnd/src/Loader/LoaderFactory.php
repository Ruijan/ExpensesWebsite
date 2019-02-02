<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/2/2019
 * Time: 4:57 PM
 */

namespace BackEnd\Loader;

use BackEnd\Loader\CSVLoader;

class LoaderFactory
{
    public function createLoader($type)
    {
        switch ($type) {
            case "CSVLoader":
                return new CSVLoader();
            default:
                throw new \InvalidArgumentException("type " . $type . " is not recognized");
        }
    }
}