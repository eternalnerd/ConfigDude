<?php

namespace EternalNerd\ConfigDude;

use EternalNerd\ConfigDude\Section;
use EternalNerd\ConfigDude\Token;
use InvalidArgumentException;

class Parser
{
    private $lines = [];
    private $sections = [];

    function __construct(string $fileName)
    {
        if(!file_exists($fileName))
        {
            throw new InvalidArgumentException("Chosen file does not exist.");
        }

        $openFile = file($fileName);
        
        $this->lines = $openFile;

        foreach($openFile as $lineNumber => $fileLine)
        {
            
            //Collect our matched tokens
            preg_match('/{{#(?:[^\/]).*?#}}/', $fileLine, $sectionHeader);
            preg_match('/{{#(?:[\/]).*?#}}/', $fileLine, $sectionFooter);
            preg_match_all('/{{.*?}}/', $fileLine, $rawTokens);

            //Processs Section Start
            if(!empty($sectionHeader))
            {
                // Let's see if we're inside of an anonymous section, if so ditch it for the new section.
                if(isset($currentSection))
                {
                    $this->sections[] = $currentSection;
                    unset($currentSection);
                }

                $sectionString = preg_replace(['/#/','/{{/','/}}/'], ['','','',''],$sectionHeader)[0];
                $currentSection = new Section(explode("|",$sectionString));
            }
            
            //Process Section End
            if(!empty($sectionFooter))
            
            {

                $this->sections[] = $currentSection;

                $sectionString = preg_replace(['/#/','/{{/','/}}/','/\//'], ['','','',''],$sectionFooter)[0];
                
                unset($currentSection);   
            }
            
            //Generate anonymous sections
            if(!isset($currentSection))
            {
                $currentSection = new Section(["anonymous".$lineNumber]);

            }

            //Process tokens
            if(!empty($rawTokens))
            {
                foreach($rawTokens[0] as $rawToken)
                {
                    //Ensure we're not tokenizing a header/footer token.
                    if(strpos($rawToken, "#") === false) 
                    {
                        //Clear Cruft
                        $tokenString = preg_replace(['/{{/','/}}/'], ['','',''],$rawToken);

                        //Explode segments to array
                        $tokenArray = explode("|", $tokenString);
                        if(!$currentSection->childExists($tokenArray[0]))
                        {
                            $currentSection->addChild(new Token($tokenArray));
                        }
                    }
                }
            }
        }
    }

    public function getJsonSections() :string
    {
        return json_encode($this->sections);
    }

    public function getJsonLines() :string
    {
        return json_encode($this->lines);
    }

    public function getLines() :iterable
    {
        return $this->lines;
    }

    public function getSections() :iterable
    {
        return $this->sections;
    }

    public function renderHTML(string|array $sectionClasses = '', string|array $labelClasses = '' , string|array $inputClasses = '') :string|bool
    {
        $outputHTML = '';
        foreach($this->getSections() as $section)
        {
            $outputHTML .= $section->renderHTMLStart($sectionClasses);
            foreach($section->getChildren() as $child)
            {
                $outputHTML .= $child->renderHTML($labelClasses, $inputClasses);
                //echo "\t",($child->getDefault()) ? $child->getDefault() : "No default value","",PHP_EOL;
            }
            $outputHTML .= $section->renderHTMLEnd();
        }
        return (!empty($outputHTML))? $outputHTML : false ;
    }
}
