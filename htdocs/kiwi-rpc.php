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
//createGraphsFromTemplatesAggregate("kiwirpcsduration", "service");
//createGraphsFromTemplatesAggregate("kiwirpcsduration", "host");


$graphTemplate['test template bypass'] = array(
		'type' => 'graphite',
		'sectiontitle' => 'kiwi rpc test counters',
		'prefixpattern' => '(^statsd)',
		'hostpattern' => '(.*app.*)\.kiwi',	
//		'servicepattern' => '(rpc\..*)',
		'servicepattern' => '(rpc\..*errors|rpc\..*count)',
//		'servicepattern' => '(rpc\.blaze\.post\..*)',
		'suffixpattern' => '(counters\.count)'

//		,'firstypattern' => '(rpc\..*\.count)'
//		,'secondypattern' => '(rpc\..*\.errors)'
//		,'servicepattern' => '(rpc\.blaze\..*)'	
//		,'firstypattern' => '(rpc\.blaze\.post\.count)	
//		,'secondypattern' => '(rpc\.blaze\.post\.errors)'	
//		,'suffixpattern' => '([e,c].*\.counters\.count)'

// create a sumSeries for each y axis
//		,'sumgraphs' => 'series'
		
);




createGraphsFromTemplatesAggregate("test template bypass", "service");
//createGraphsFromTemplatesAggregate("test template bypass", "host");
//createGraphsFromTemplates("test template bypass", "host");
//createGraphsFromTemplates("test template bypass", "service");




$graphTemplate['test template bypass2'] = array(
		'type' => 'graphite',
		'sectiontitle' => 'kiwi rpc test counters',
		'prefixpattern' => '(^statsd)',
		'hostpattern' => '(.*app00)\.kiwi',	
		'servicepattern' => '(rpc\..*blaze.*\.[errors,count]\.counters)',		
//		'firstypattern' => '(rpc\.blaze\.post\.count)',		
//		'secondypattern' => '(rpc\.blaze\.post\.errors)',		
		'suffixpattern' => '(counters\.count)'
);
//createGraphsFromTemplatesAggregate("test template bypass2", "service");




//createGraphsFromTemplates("kiwirpcs", "host");
//createGraphsFromTemplates("kiwirpcs", "service");

//createGraphsFromTemplates("kiwitest");



 printTimer('pre template');
//exit;
/** actually draws the page */
include 'phplib/template.php';
