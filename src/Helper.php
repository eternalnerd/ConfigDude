<?php

namespace EternalNerd\ConfigDude;

class Helper
{
    public static function toCamelCase(string $string) :string 
    {
        $string = ucwords(($string), " ");
        $string = preg_replace("/[^a-zA-Z0-9]/", "", $string);
        $string = preg_replace_callback("/^(\w)/",fn($letter) => strtolower($letter[0]), $string);
        return $string;
    }
    
}