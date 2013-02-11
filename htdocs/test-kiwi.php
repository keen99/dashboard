<?php
require_once 'phplib/Dashboard.php';
require_once 'easod/functions.php';


//startTimer();





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
//		,'rightaxisfunctions' => 'secondYAxis,stacked,keepLastValue'
		,'rightaxisfunctions' => 'stacked,keepLastValue'


//		,'leftaxispattern' => '(rpc\.blaze\.post\.count)	
//		,'rightaxispattern' => '(rpc\.blaze\.post\.errors)'	
//		,'suffixpattern' => '([e,c].*\.counters\.count)'
//		,'colors' => array('red', 'yellow', 'olive')

		
);



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
//createGraphsFromTemplates("test template bypass", "host", true, true);



//$hide_deploys=true;





buildTemplateDropDown();
buildGraphOptions();

if ( $_GET['graphtemplate'] === 'empty' ) {
	echo '<div class=notice>please choose a graph template<br></div>';
	$graphs=array();
} else {
	createGraphsFromTemplates($_GET['graphtemplate'], $_GET['groupby'], $_GET['sum'], $_GET['agg']);
}



//this sets up a tab-navigation between pages.  also adds an "up to home" link
$tabs = Local_Dashboard::$KIWI_TABS;
$tab_url = Dashboard::getTabUrl(__FILE__);


// actually adds it right before the main graph frame.
// and adds html to the header
//$html_for_header=...

$html_for_header=generateListBox($graphs);


/** the title used for the page */
$title = $graphTemplate[$_GET['graphtemplate']]['sectiontitle'];
$namespace="";


//extra body html after all the graphs
//$additional_html="this is cool shtuff here<br>";



/*
echo "\n\n";
echo '<div id="frame" class=menu style="background-color:#FFD700;width:100px;float:left;">';
echo "lets try another box";
echo "/div>";
*/

/*
//it's a messagey box - full width
echo "<div class=notice>";
echo "notice is here<br>";
echo "</div>";

//more of a section header, but just lands at the top...
echo "<div class=section>";
echo "section is here<br>";
echo "</div>";

*/



 printTimer('pre template');
//exit;
/** actually draws the page */
include 'phplib/template.php';
