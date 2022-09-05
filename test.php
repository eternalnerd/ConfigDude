<?php

require_once('vendor/autoload.php');

use EternalNerd\ConfigDude\Parser;

$file = "config.file";

$parser = new Parser($file);

foreach($parser->getSections() as $section)
{
    echo $section->renderHTMLStart();
    foreach($section->getChildren() as $child)
    {
        echo $child->renderHTML();
        //echo "\t",($child->getDefault()) ? $child->getDefault() : "No default value","",PHP_EOL;
    }
    echo $section->renderHTMLEnd();
}

