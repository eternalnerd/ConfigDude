<?php

require_once('vendor/autoload.php');

use EternalNerd\ConfigDude\Parser;

$file = "test.file";

$parser = new Parser($file);

echo $parser->renderHTML(['testClass'], ['testLabelClass'], ['testInputClass']);

