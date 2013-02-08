<?php
require_once 'phplib/Dashboard.php';
require_once 'easod/functions.php';

/** the title used for the page */
$title = 'this is my fun stuff';


addGraphTemplate(
	"first template", 
	"cool section - kiwi rpc", 
	"^.*",
	".*\.kiwi",
	"rpc.*",
	"count\.counters\.count"
);


createGraphsFromTemplates("first template");



/** actually draws the page */
include 'phplib/template.php';

