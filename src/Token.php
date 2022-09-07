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
    private $twig;
    private $validationPatterns;
    
    public function __construct(array $array)
    {
        $this->prettyName = array_shift($array);
        $this->name = Helper::toCamelCase($this->prettyName);

        $this->validationPatterns = [
            'ipAddress'                 => '((25[0-5]|(2[0-4]|1\d|[1-9]|)\d)\.?\b){4}',
            'macAddress'                => '([0-9a-fA-F]{2}[:-]?){6}',
            'juniperInterfaceName'      => '(irb|ae(12[0-8]|1[0-1][0-9]|[0-9][0-9]|[0-9])|[a-z]?[a-z][a-z]-[0-9](\/?([0-9][0-9]|[0-9]))(\/?([0-9][0-9]|[0-9])))',
        ];

        $loader = new \Twig\Loader\ArrayLoader([
            'range'    => Template::range(),
            'checkBox' => Template::checkBox(),
            'textArea' => Template::textArea(),
            'inputString' => Template::inputString(),
            'inputInteger' => Template::inputInteger()
        ]);

        $this->twig = new \Twig\Environment($loader, [
            'cache' => false,
        ]);

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

    public function getValidationType() :string
    {
        return (isset($this->validationPatterns[$this->type])) ? $this->validationPatterns[$this->type] : '';
    }

    public function renderHTML(array $labelClasses = [], array $inputClasses = []) :string
    {
        $template = match($this->type)
        {
            'range'     => 'range',
            'bool'      => 'checkBox',
            'textArea'  => 'textArea',
            'int'       => 'inputInteger',
            default     => 'inputString'
        };

        return $this->twig->render($template,[ 
            'vars' => $this->toArray(),
            'classes' => [
                'labelClasses' => $labelClasses, 
                'inputClasses' => $inputClasses
            ],
            'validation' => $this->getValidationType()
        ]);
    }

    public function setName($newName) :void
    {
        $this->name = $newName;
    }

    public function toArray() :array
    {
        foreach($this as $key => $val)
        {
            $out[$key] = $val;
        }
        return $out;
    }
}