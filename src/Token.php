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

    public function renderHTML(string|array $labelClasses, string|array $inputClasses) :string
    {
        $html = match($this->type)
        {
            'bool'      => sprintf('<fieldset>%s<label class="%s" for="%s">%s%s<input type="checkbox" class="%s" name="%s" %s/>%s</label>%s</fieldset>%s',
                                        PHP_EOL,
                                        (is_array($labelClasses)) ? implode(" ", $labelClasses) : $labelClasses, 
                                        $this->name,
                                        $this->prettyName,
                                        PHP_EOL,
                                        (is_array($inputClasses)) ? implode(" ", $inputClasses) : $inputClasses,
                                        $this->name,
                                        $this->defaultValue,
                                        PHP_EOL,PHP_EOL,PHP_EOL),
            'textArea'  => sprintf('<label class="%s" for="%s">%s%s<textarea class="%s" name="%s">%s</textarea>%s</label>%s',
                                        (is_array($labelClasses)) ? implode(" ", $labelClasses) : $labelClasses, 
                                        $this->name,
                                        $this->prettyName,
                                        PHP_EOL,
                                        (is_array($inputClasses)) ? implode(" ", $inputClasses) : $inputClasses,
                                        $this->name,
                                        $this->defaultValue,
                                        PHP_EOL,PHP_EOL),
            default     => sprintf('<label class="%s" for="%s">%s%s<input class="%s" type="text" name="%s" value="%s" />%s</label>%s',
                                        (is_array($labelClasses)) ? implode(" ", $labelClasses) : $labelClasses,
                                        $this->name,
                                        $this->prettyName,
                                        PHP_EOL,
                                        (is_array($inputClasses)) ? implode(" ", $inputClasses) : $inputClasses,
                                        $this->name,
                                        $this->defaultValue,
                                        PHP_EOL,PHP_EOL),
        };
        return $html;
    }
}