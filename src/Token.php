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
    private $twigLoader;

    public function __construct(array $array)
    {
        $this->prettyName = array_shift($array);
        $this->name = Helper::toCamelCase($this->prettyName);
        
        $loader = new \Twig\Loader\FilesystemLoader('templates');
        $this->twig = new \Twig\Environment($loader, [
            'cache' => 'templates/cache',
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

    public function renderHTML(array $labelClasses = [], array $inputClasses = []) :string
    {
        $template = match($this->type)
        {
            'bool'      => 'checkBox.html',
            'textArea'  => 'textArea.html',
            default     => 'input.html'
        };

        return $this->twig->render($template,[ 
            'vars' => $this->toArray(),
            'classes' => [$labelClasses, $inputClasses]
        ]);
    }

    public function toArray() :array
    {
        foreach($this as $key => $val)
        {
            $out[$key] = $val;
        }
        return $out;
    }

    function twigAddPath($path)
    {
        return $this->twigLoader->addPath($path);
    }
}