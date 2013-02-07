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
//		'servicepattern' => '(rpc\..*errors|rpc\..*count)',
//		'servicepattern' => '(rpc\.blaze\.post\..*)',
		'suffixpattern' => '(errors\.counters\.count|count\.counters\.count)'

		,'leftaxispattern' => '(rpc\..*\.count)'
		,'leftaxisalias' => 'count'
		,'leftaxisfunctions' => 'keepLastValue'
		,'rightaxispattern' => '(rpc\..*\.errors)'
		,'rightaxisalias' => 'errors'
		,'rightaxisfunctions' => 'keepLastValue'
//		,'servicepattern' => '(rpc\.blaze\..*)'	
		,'servicepattern' => '(rpc\.auction\..*)'	
//		,'leftaxispattern' => '(rpc\.blaze\.post\.count)	
//		,'rightaxispattern' => '(rpc\.blaze\.post\.errors)'	
//		,'suffixpattern' => '([e,c].*\.counters\.count)'
//		,'colors' => array('red', 'yellow', 'olive')

		
);




//createGraphsFromTemplates("test template bypass", "service");

//createGraphsFromTemplates("test template bypass", "service");
createGraphsFromTemplatesHack("test template bypass", "service", false, false);
//createGraphsFromTemplatesHack("test template bypass", "service", false, true);


//createGraphsFromTemplates("test template bypass", "host");

//createGraphsFromTemplatesAggregate("test template bypass", "service");
//createGraphsFromTemplatesAggregate("test template bypass", "host", true);
//createGraphsFromTemplatesAggregate("test template bypass", "host");

//createGraphsFromTemplatesAggregate("test template bypass", "service", true);

//createGraphsFromTemplatesAggregate("test template bypass", "host");
//createGraphsFromTemplates("test template bypass", "host");
//createGraphsFromTemplates("test template bypass", "service");




$graphTemplate['test template bypass2'] = array(
		'type' => 'graphite',
		'sectiontitle' => 'kiwi rpc test counters',
		'prefixpattern' => '(^statsd)',
		'hostpattern' => '(.*app00)\.kiwi',	
		'servicepattern' => '(rpc\..*blaze.*\.[errors,count]\.counters)',		
//		'leftaxispattern' => '(rpc\.blaze\.post\.count)',		
//		'rightaxispattern' => '(rpc\.blaze\.post\.errors)',		
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
