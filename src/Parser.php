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
            //Cache full line
            $currentSection->addLine($fileLine);
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

    public function buildOutputFile($outputFile) :bool
    {
        
        if(!$file = fopen($outputFile,'w+'))
        {
            throw new \Exception("Unable to open file for writing.");
        }

        foreach($this->buildOutputLines() as $line)
        {
            if(fwrite($file, $line) === false)
            {
                throw new \Exception("Unable to write line to file.");
            }
        }
        fclose($file);
    
        return true;
    }

    public function buildOutputLines() :array
    {
        $repeatSectionLines = [];
        $singleSectionLines = [];
        // Iterate sections to get lines
        foreach($this->getSections() as $section)
        {
            // If this is a repeatable section, do the replace operation on each line
            if($section->getRepeatable())
            {
                for($x = 1; $x <= $section->getMax(); $x++)
                {
                    $repeatSectionLines[$x][] = $section->renderReplacedLines($this->getJsonValues(), $x);
                }
            }
            //If not, just do it on this section and move on.
            else
            {
                $singleSectionLines[] = $section->renderReplacedLines($this->getJsonValues());
            }

            if(isset($repeatSectionLines))
            {
                foreach($repeatSectionLines as $block)
                {
                    foreach($block as $blockLine)
                    {
                        $singleSectionLines[] = $blockLine;
                    }
                }
                unset($repeatSectionLines);
            }
        }
        foreach($singleSectionLines as $section){
            foreach($section as $line)
            {
                $finalOutputLines[] = $line;
            }
        }
        
        return $finalOutputLines;
    }

    public function getJsonValues() :iterable
    {
        if(!empty($this->jsonTokenArray))
        {
            foreach($this->jsonTokenArray as $key => $val)
            {
                $out[$key] = $val;
            }
            return $out;
        }
    }

    public function getLines() :iterable
    {
        return $this->lines;
    }

    public function getSections() :iterable
    {
        return $this->sections;
    }

    public function loadJsonTokenArrayString(string $jsonString) :bool
    {
        return $this->jsonTokenArray = json_decode($jsonString);
    }

    public function loadJsonTokenArrayFile(string $fileName) :bool
    {
        if(!file_exists($fileName))
        {
            throw new InvalidArgumentException("Chosen json file does not exist.");
        }

        return $this->jsonTokenArray = json_decode(file_get_contents($fileName));
    }

    public function renderHTML(array $sectionClasses = [], array $labelClasses = [] , array $inputClasses = []) :string|bool
    {
        $outputHTML = '';
        foreach($this->getSections() as $section)
        {
            if($section->getRepeatable() && !empty($section->getMax()))
            {
                for($x = 1; $x <= $section->getMax(); $x++)
                {
                    $outputHTML .= $section->renderHTMLStart($sectionClasses, $x);
                    foreach($section->getChildren() as $child)
                    {
                        $newChild = clone $child;
                        $newChild->setName($child->getName()."-".$x);
                        $outputHTML .= $newChild->renderHTML($labelClasses, $inputClasses);
                    }
                    $outputHTML .= $section->renderHTMLEnd();
                }
            }
            else
            {
                $outputHTML .= $section->renderHTMLStart($sectionClasses);
                foreach($section->getChildren() as $child)
                {
                    $outputHTML .= $child->renderHTML($labelClasses, $inputClasses);
                }
                $outputHTML .= $section->renderHTMLEnd();
            }
        }
        return (!empty($outputHTML))? $outputHTML : false ;
    }

    public function renderJsonSections() :string
    {
        return json_encode($this->sections);
    }

    public function renderJsonLines() :string
    {
        return json_encode($this->lines);
    }
}
