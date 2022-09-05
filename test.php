<?php

require_once('vendor/autoload.php');

use EternalNerd\ConfigDude\Parser;

$file = "config.file";

$parser = new Parser($file);

echo $parser->renderHTML();

