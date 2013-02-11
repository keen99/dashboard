<?php
require_once 'phplib/Dashboard.php';
require_once 'easod/functions.php';


//startTimer();

/** the title used for the page */
$title = 'kiwi rpcs';
$namespace="";


$graphTemplate['empty'] = array(
	'sectiontitle' => 'PICK ONE'
);


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


$additional_controls="";
//this isn't really good enough to carry stuff forward, we need to limit it so it doesn't goet longer and longer and longer
//foreach ($_GET as $key => $value) {
//    $additional_controls .= "<input type='hidden' name='$key' value='$value'/>";
//}

//$additional_controls .= "<input type='checkbox' name='whichgraph' value='0' />";
$additional_controls .= "<span class='control'>";
//default to service
if ( !isset($_GET['groupby']) ) { $_GET['groupby'] = 'service'; }
$additional_controls .= Controls::buildRadio('groupby', 'Group By', array('service' => 'Service' , 'host' => 'Host'), $_GET['groupby'] );
$additional_controls .= "</span><span class='control'>";
if (! isset($_GET['sum']) ) { $_GET['sum'] = 0; }
$additional_controls .= Controls::buildCheckbox('sum', 'Sum Items', $_GET['sum'] );
if (! isset($_GET['agg']) ) { $_GET['agg'] = 0; }
$additional_controls .= Controls::buildCheckbox('agg', 'Aggregate', $_GET['agg'] );
$additional_controls .= '</span>';
$additional_controls .= "<span class='control'>";


if ( !isset($_GET['graphtemplate']) ) {
	$_GET['graphtemplate'] = 'empty';
}

if ( $_GET['graphtemplate'] === 'empty' ) {

	echo '<div class=notice>please choose a graph template<br></div>';
	$graphs=array();
} else {
	createGraphsFromTemplates($_GET['graphtemplate'], $_GET['groupby'], $_GET['sum'], $_GET['agg']);

}

//$additional_controls .= Controls::buildSelect('selectfun', 'select one fun', array('service' => 'Service' , 'host' => 'Host'), $_GET['elected'] );
foreach ( $graphTemplate as $key => $value) { $graphTemplates[$key] = $value['sectiontitle'];	}
$additional_controls .= Controls::buildSelect('graphtemplate', 'Graph Template', $graphTemplates, $_GET['graphtemplate'] );


//createGraphsFromTemplates("test template bypass", $_GET['groupby'], $_GET['sum'], $_GET['agg']);


/*
if ( isset($_GET['whichgraph']) ) {
	echo "got whichgraph ".  $_GET['whichgraph'] . "<br>";

}

if ( isset($_GET['whichgraph']) ) {

	switch($_GET['whichgraph']) {
		case 0:
			createGraphsFromTemplates("test template bypass", "service", false, false);
		case 1:
			createGraphsFromTemplates("test template bypass", "host", false, false);

	}
} else {

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
}
*/

// adds another input under the deploys inputs...
//$additional_controls="<input type='checkbox' name='hype_deploys' value='true'/><label>Hype All Deploys</label>";

//$hide_deploys=true;



//this sets up a tab-navigation between pages.  also adds an "up to home" link
$tabs = Local_Dashboard::$KIWI_TABS;
$tab_url = Dashboard::getTabUrl(__FILE__);


// actually adds it right before the main graph frame.
// and adds html to the header
//$html_for_header=...

//extra body html after all the graphs
//$additional_html="this is cool shtuff here<br>";

function generateBox($graphs) {

	$graphcount=0;
	$sectioncount=0;
	$boxes="";
	while ($section = current($graphs) ){ 
		//this is our section header
		$sectionkey=key($graphs);
		$boxes.="<a href=#". preg_replace("/[^a-z0-9]/", "", strtolower($sectionkey)) . ">$sectionkey</a><br>";
		$sectioncount++;
		while ($graph = current($graphs[$sectionkey]) ){ 		
			//this is our graph itself
//dont show these yet we cant link to them
//			$graphkey=key($graphs[$sectionkey]);
//			$boxes.="<a href=#" . preg_replace("/[^a-z0-9]/", "", strtolower($graphkey)) . ">$graphkey</a><br>";

			$graphcount++;
		next($graphs[$sectionkey]);
		}		

		next($graphs);
	}

	$boxcontent="<b>Sections: $sectioncount</b><br>";	
	$boxcontent.="<b>Graphs: $graphcount</b><hr>";	
	$boxcontent.=$boxes;
	$boxcontent='<div id=menu style="background-color:#fff;
					width:10%;
					height:auto;
					font-size:10px;
					float: left;">
					' . $boxcontent . '
					</div>
					<div id=frame style="width:90%;"">';
	return $boxcontent;
};

//echo generateBox($graphs);

//exit;

//if (!empty($graphs)) {
$html_for_header=generateBox($graphs);
//}




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
