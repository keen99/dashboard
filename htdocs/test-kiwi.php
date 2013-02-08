<?php
require_once 'phplib/Dashboard.php';
require_once 'easod/functions.php';


startTimer();

/** the title used for the page */
$title = 'kiwi rpcs';
$namespace="";


$graphTemplate['test template bypass'] = array(
		'type' => 'graphite'
		,'sectiontitle' => 'kiwi rpc test counters'
		,'prefixpattern' => '(^statsd)'
		,'hostpattern' => '(.*app.*)\.kiwi'
		,'servicepattern' => '(rpc\..*)'
		//,'servicepattern' => '(rpc\..*errors|rpc\..*count)'  //this shouldnt and doesnt work
//		'servicepattern' => '(rpc\.blaze\.post\..*)'
//		,'servicepattern' => '(rpc\.blaze\..*)'	
//		,'servicepattern' => '(rpc\.auction\..*)'	
		,'suffixpattern' => '(errors\.counters\.count|count\.counters\.count)'

		,'leftaxispattern' => '(rpc\..*\.count)'
		,'leftaxisalias' => 'count'
		,'leftaxisfunctions' => 'keepLastValue'
		,'rightaxispattern' => '(rpc\..*\.errors)'
		,'rightaxisalias' => 'errors'
// so we probably will need some specific logic around stacks, wen we can'cant use them
		,'rightaxisfunctions' => 'secondYAxis,stacked,keepLastValue'
	,'rightaxisfunctions' => 'stacked,keepLastValue'


//		,'leftaxispattern' => '(rpc\.blaze\.post\.count)	
//		,'rightaxispattern' => '(rpc\.blaze\.post\.errors)'	
//		,'suffixpattern' => '([e,c].*\.counters\.count)'
//		,'colors' => array('red', 'yellow', 'olive')

		
);



//1
//  group by service, each service and each host on it's own graph - err/count split
//createGraphsFromTemplates("test template bypass", "service", false, false);
//5
//  group by service, each service and each host on it's own graph - err/count combined and sum
//createGraphsFromTemplates("test template bypass", "service", true, false);
//2
//  group by service, aggregate each service (all hosts) onto one graph
//createGraphsFromTemplates("test template bypass", "service", false, true);
//6
//  group by service, aggregate each service (all hosts) onto one graph and sum
//createGraphsFromTemplates("test template bypass", "service", true, true);
//3
//  group by host, each service and each host on it's own graph - split err/count
//createGraphsFromTemplates("test template bypass", "host", false, false);
//4 -- too large w/ all rpcs
//  group by host, aggregate each host (all services) onto one graph
//createGraphsFromTemplates("test template bypass", "host", false, true);
//7
//  group by host, each service and each host on it's own graph - combined errr/count
//createGraphsFromTemplates("test template bypass", "host", true, false);
//8
//  group by host, aggregate each host (all services) onto one graph
createGraphsFromTemplates("test template bypass", "host", true, true);


// adds another input under the deploys inputs...
//$additional_controls="<input type='checkbox' name='hype_deploys' value='true'/><label>Hype All Deploys</label>";

//$hide_deploys=true;

$additional_controls="";
//this isn't really good enough to carry stuff forward, we need to limit it so it doesn't goet longer and longer and longer
foreach ($_GET as $key => $value) {
    $additional_controls .= "<input type='hidden' name='$key' value='$value'/>";
}

$tabs = Local_Dashboard::$KIWI_TABS;
$tab_url = Dashboard::getTabUrl(__FILE__);


 printTimer('pre template');
//exit;
/** actually draws the page */
include 'phplib/template.php';
