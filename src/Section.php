<?php

namespace EternalNerd\ConfigDude;

class Section
{
    private $children = [];
    private $min;
    private $max;
    private $name;
    private $prettyName;
    private $repeatable = false;
    private $twig;

    public function __construct(array $array)
    {
        $this->prettyName = array_shift($array);
        $this->name = Helper::toCamelCase($this->prettyName);
        
        $loader = new \Twig\Loader\FilesystemLoader(['templates','../vendor/eternalnerd/config-dude/templates']);
        $this->twig = new \Twig\Environment($loader, [
            'cache' => 'templates/cache',
        ]);

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

                case strpos($item, "repeatable") !== false:
                    $this->repeatable = true;
                break;
                default:

                break;
            }
        }
    }

    public function addChild($child) :bool
    {
        return array_push($this->children, $child);
    }

    public function childExists($childName): bool
    {
        if(empty($this->children))
        {
            return false;
        }
        
        foreach($this->getChildren() as $child)
        {
            if($child->getName() == $childName) 
            {
                return true;
            }    
        }
        return false;
    }

    public function getChildren() :iterable
    {
        return $this->children;
    }

    public function getName() :string
    {
        return $this->name;
    }

    public function getMin() :int
    {
        return $this->min;
    }

    public function getMax() :int
    {
        return $this->max;
    }    

    public function getRepeatable() :bool
    {
        return $this->repeatable;
    }

    public function renderHTMLEnd() :string
    {
        return (!empty($this->children)) ? $this->twig->render('sectionEnd.html') : '' ;
    }

    public function renderHTMLStart(array $sectionClasses = []) :string
    {
        return (!empty($this->children)) ? $this->twig->render('sectionStart.html', [
            'vars' => $this->toArray(),
            'classes' => $sectionClasses
        ]) : '' ;
    }

    function toArray() :array
    {
        foreach($this as $key => $val)
        {
            $out[$key] = $val;
        }
        return $out;
    }
}