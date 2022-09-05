<?php

namespace EternalNerd\ConfigDude;

use EternalNerd\ConfigDude\Helper;

class Token
{
    private $defaultValue;
    private $min;
    private $max;
    private $name;
    private $prettyName;
    private $type;

    public function __construct(array $array)
    {
        $this->prettyName = array_shift($array);
        $this->name = Helper::toCamelCase($this->prettyName);
        if(count($array) < 1)
        {
            $this->type = 'string';
        }
        else
        {
            $this->type = array_shift($array);
            foreach($array as $item)
            {
                switch(true)
                {
                    case strpos($item, "min") !== false: 
                        $this->min = preg_replace('/[^0-9]+/','',$item);
                    break;
    
                    case strpos($item, "max") !== false:
                        $this->max = preg_replace('/[^0-9]+/','',$item);
                    break;
    
                    case strpos($item, "default") !== false:
                        $this->defaultValue = explode("=",preg_replace("/[\"\']/","",$item))[1];
                    break;
    
                    default:
    
                    break;
                }
            }
        }
    }

    public function getDefault(): string|bool
    {
        return (!empty($this->defaultValue)) ? $this->defaultValue : false;
    }

    public function getMin() :int
    {
        return $this->min;
    }

    public function getMax() :int
    {
        return $this->max;
    }

    public function getName() :string
    {
        return $this->name;
    }

    public function getPrettyName() :string
    {
        return $this->prettyName;
    }

    public function getType() :string|bool
    {
        return (!empty($this->type)) ? $this->type : false ;
    }

    public function renderHTML() :string
    {
        $html = match($this->type)
        {
            default  => sprintf('<label for="%s">%s%s<input type="text" name="%s" value="%s" />%s</label>%s',
                                        $this->name,
                                        $this->prettyName,
                                        PHP_EOL,
                                        $this->name,
                                        $this->defaultValue,
                                        PHP_EOL,PHP_EOL),
        };
        return $html;
    }
}