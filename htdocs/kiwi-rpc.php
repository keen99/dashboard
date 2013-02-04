<?php
require_once 'phplib/Dashboard.php';
require_once 'easod/functions.php';


startTimer();

/** the title used for the page */
$title = 'kiwi rpcs';
$namespace="";

addGraphTemplate(
	"kiwirpcs", 
	"kiwi rpc counts", 
	"(^statsd)",
	"(.*.*)\.kiwi",
	"(rpc.*)",
	"(count\.counters\.count)"
);

addGraphTemplate(
	"kiwirpcsduration", 
	"kiwi rpc duration avg", 
	"(^statsd)",
	"(.*.*)\.kiwi",
	"(rpc.*)",
	"(duration\.avg\.counters\.count)"
);

//createGraphsFromTemplatesAggregate("kiwirpcs", "host");
//createGraphsFromTemplatesAggregate("kiwirpcs", "service");

//createGraphsFromTemplatesAggregate("kiwirpcs", "service");
createGraphsFromTemplatesAggregate("kiwirpcsduration", "service");
//createGraphsFromTemplatesAggregate("kiwirpcsduration", "host");


//createGraphsFromTemplates("kiwirpcs", "host");
//createGraphsFromTemplates("kiwirpcs", "service");

//createGraphsFromTemplates("kiwitest");



 printTimer('pre template');
exit;
/** actually draws the page */
include 'phplib/template.php';
