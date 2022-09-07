<?php

namespace EternalNerd\ConfigDude;

class Section
{
    private $children = [];
    private $lines = [];
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
        $loader = new \Twig\Loader\ArrayLoader([
            'sectionStart'  => Template::sectionStart(),
            'sectionEnd'    => Template::sectoinEnd()
        ]);
        $this->twig = new \Twig\Environment($loader, [
            'cache' => false,
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

    public function addLine($line) :bool
    {
        return array_push($this->lines, $line);
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

    public function getLines() :iterable
    {
        return $this->lines;
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
        return (!empty($this->children)) ? $this->twig->render('sectionEnd') : '' ;
    }

    public function renderHTMLStart(array $sectionClasses = [], int $instance = null) :string
    {
        return (!empty($this->children)) ? $this->twig->render('sectionStart', [
            'vars' => $this->toArray(),
            'classes' => $sectionClasses,
            'instance' => $instance
        ]) : '' ;
    }

    public function renderReplacedLines($values, $iterator = '')
    {
        foreach($this->getLines() as $line)
        {
            $outLine = $line;
            //Collect our matched tokens
            preg_match('/{{#(?:[^\/]).*?#}}/', $line, $sectionHeader);
            preg_match('/{{#(?:[\/]).*?#}}/', $line, $sectionFooter);
            preg_match_all('/{{.*?}}/', $line, $rawTokens);

            if(!empty($sectionHeader))
            {
                continue;
            }

            if(!empty($sectionFooter))
            {
                continue;
            }

            if(!empty($rawTokens))
            {
                foreach($rawTokens[0] as $token){
                    if(strpos($token, "#") === false) 
                        {
                            //Clear Cruft
                            $tokenString = preg_replace(['/{{/','/}}/'], ['','',''],$token);

                            //Explode segments to array
                            $tokenArray = explode("|", $tokenString);
                            $value = (empty($iterator)) ? $values[Helper::toCamelCase($tokenArray[0])] : $values[Helper::toCamelCase($tokenArray[0]).'-'.$iterator];
                            $outLine = preg_replace('/{{'.$tokenArray[0].'.*?}}/',$value,$outLine);
                        }
                }
                $outLines[] = $outLine;
            }
        }
        return $outLines;
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