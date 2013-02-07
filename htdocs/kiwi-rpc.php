<?php
require_once 'phplib/Dashboard.php';
require_once 'easod/functions.php';


startTimer();

/** the title used for the page */
$title = 'kiwi rpcs';
$namespace="";
/*
addGraphTemplate(
	"kiwirpcs", 
	"kiwi rpc counts", 
	"(^statsd)",
	"(.*.*)\.kiwi",
	"(rpc.*)",
	"(count\.counters\.count)"
);
*/
/*
addGraphTemplate(
	"kiwirpcsduration", 
	"kiwi rpc duration avg", 
	"(^statsd)",
	"(.*.*)\.kiwi",
	"(rpc.*)",
	"(duration\.avg\.counters\.count)"
);
*/

//createGraphsFromTemplatesAggregate("kiwirpcs", "host");
//createGraphsFromTemplatesAggregate("kiwirpcs", "service");

//createGraphsFromTemplatesAggregate("kiwirpcs", "service");
//createGraphsFromTemplatesAggregate("kiwirpcsduration", "service");
//createGraphsFromTemplatesAggregate("kiwirpcsduration", "host");


$graphTemplate['kiwiallrpccounts'] = array(
		'sectiontitle' => 'kiwi all rpc counts'
		,'prefixpattern' => '(^statsd)'
		,'hostpattern' => '(.*app.*)\.kiwi'
		,'servicepattern' => '(rpc\..*)'
		,'suffixpattern' => '(errors\.counters\.count|count\.counters\.count)'

		,'leftaxispattern' => '(rpc\..*\.count)'
		,'leftaxisalias' => 'count'
		,'leftaxisfunctions' => 'keepLastValue'
		,'rightaxispattern' => '(rpc\..*\.errors)'
		,'rightaxisalias' => 'errors'
// so we probably will need some specific logic around stacks, wen we can'cant use them
//		,'rightaxisfunctions' => 'secondYAxis,stacked,keepLastValue'
		,'rightaxisfunctions' => 'stacked,keepLastValue'		
);



$graphTemplate['kiwiallrpcduration'] = array(
		'sectiontitle' => 'kiwi all rpc durations'
		,'prefixpattern' => '(^statsd)'
		,'hostpattern' => '(.*app.*)\.kiwi'
		,'servicepattern' => '(rpc\..*)'
//		,'suffixpattern' => '(errors\.counters\.count|count\.counters\.count)'
		,'suffixpattern' => '(duration\.avg\.counters\.count|duration\.max\.counters\.count)'


		,'leftaxispattern' => '(rpc\..*\.duration.avg)'
		,'leftaxisalias' => 'avg'
		,'leftaxisfunctions' => 'keepLastValue'
		,'rightaxispattern' => '(rpc\..*\.duration.max)'
		,'rightaxisalias' => 'max'
		,'rightaxisfunctions' => 'keepLastValue'		
);

createGraphsFromTemplates("kiwiallrpcduration", "service", true, true);


//1
//  group by service, each service and each host on it's own graph - err/count split
//createGraphsFromTemplates("kiwiallrpccounts", "service", false, false);
//5
//  group by service, each service and each host on it's own graph - err/count combined and sum
//createGraphsFromTemplates("kiwiallrpccounts", "service", true, false);
//2
//  group by service, aggregate each service (all hosts) onto one graph
//createGraphsFromTemplates("kiwiallrpccounts", "service", false, true);
//6
//  group by service, aggregate each service (all hosts) onto one graph and sum
//createGraphsFromTemplates("kiwiallrpccounts", "service", true, true);
//3
//  group by host, each service and each host on it's own graph - split err/count
//createGraphsFromTemplates("kiwiallrpccounts", "host", false, false);
//4 -- too large w/ all rpcs
//  group by host, aggregate each host (all services) onto one graph
//createGraphsFromTemplates("kiwiallrpccounts", "host", false, true);
//7
//  group by host, each service and each host on it's own graph - combined errr/count
//createGraphsFromTemplates("kiwiallrpccounts", "host", true, false);
//8
//  group by host, aggregate each host (all services) onto one graph
//createGraphsFromTemplates("kiwiallrpccounts", "host", true, true);






$graphTemplate['kiwiallrpccounts2'] = array(
		'type' => 'graphite',
		'sectiontitle' => 'kiwi rpc test counters',
		'prefixpattern' => '(^statsd)',
		'hostpattern' => '(.*app00)\.kiwi',	
		'servicepattern' => '(rpc\..*blaze.*\.[errors,count]\.counters)',		
//		'leftaxispattern' => '(rpc\.blaze\.post\.count)',		
//		'rightaxispattern' => '(rpc\.blaze\.post\.errors)',		
		'suffixpattern' => '(counters\.count)'
);






 printTimer('pre template');
//exit;
/** actually draws the page */
include 'phplib/template.php';
