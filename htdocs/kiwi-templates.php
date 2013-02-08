<?php
require_once 'phplib/Dashboard.php';
require_once 'easod/functions.php';


//startTimer();

/** the title used for the page */
$title = 'kiwi rpcs';
$namespace="";


$tabs = Local_Dashboard::$KIWI_TABS;
$tab_url = Dashboard::getTabUrl(__FILE__);

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

