<?php


$graphTemplate['kiwiallrpccounts'] = array(
		'sectiontitle' => 'kiwi all rpc counts'
		,'prefixpattern' => '(^statsd)'
		,'hostpattern' => '(.*app.*)\.kiwi'
		,'servicepattern' => '(rpc\..*)'
		,'suffixpattern' => '(errors\.counters\.count|count\.counters\.count)'

		,'metricpatterns' => array (
			'(rpc\..*\.count\.)',
			'(rpc\..*\.errors\.)'
		)
		,'metricaliases' => array (
			'count',
			'errors'
		)
		,'metricfunctions' => array (
			'keepLastValue',
			'stacked,keepLastValue'
		)
);



$graphTemplate['kiwiallrpcduration'] = array(
		'sectiontitle' => 'kiwi all rpc durations'
		,'prefixpattern' => '(^statsd)'
		,'hostpattern' => '(.*app00.*)\.kiwi'
		,'servicepattern' => '(rpc\..*)'
		,'suffixpattern' => '(duration\.min\.counters\.count|duration\.avg\.counters\.count|duration\.max\.counters\.count)'

		,'metricpatterns' => array (
			'(rpc\..*\.duration\.min)',
			'(rpc\..*\.duration\.avg)',
			'(rpc\..*\.duration\.max)'
		)
		,'metricaliases' => array (
			'min',
			'avg',
			'max'
		)
		,'metricfunctions' => array (
			'keepLastValue',
			'keepLastValue',
			'keepLastValue'
		)	
);




$graphTemplate['cactipoller'] = array(
		'sectiontitle' => 'cacti poller stats'
		,'prefixpattern' => '(^statsd)'
//		,'hostpattern' => '(.*uk2-r-d5.*)'
		,'hostpattern' => '(.*)'
		,'servicepattern' => '(cacti\.poller)'
		,'suffixpattern' => '(Hosts\.counters\.count|Time\.counters\.count)'

		,'metricpatterns' => array (
		'(cacti\.poller\.Hosts\..*)'
		,'(cacti\.poller\.Time\..*)'
		)
		,'metricaliases' => array (
			'Hosts',
			'Time'
		)
		,'metricfunctions' => array (
			'keepLastValue',
			'keepLastValue'		)	
		//dont use these for this graph because it's a randomized set. 
//		,'colors' => array (
//			'E6D883aa',
//			'4444FFFF'
//		)
	// only things supported by getGraphiteDashboardHTML
		,'extraflags' => array(
			'vtitle' => 'ms'
			//doesnt do any good w/o changing opaque..
			,'area_mode' => 'first'
		//override the magiced set of colors and use a fixed list
			,'colors' => array (
				'E6D883aa', //the extra aa makes this translucent..
				'4444FFFF'
			)
			,'y_min' => '0'
		)
		//bgcolor=FFFFFF&fgcolor=000000&majorGridLineColor=F3bfbf&minorGridLineColor=dedede&areaAlpha=.75&yMin=0
		//vtitle=ms
		//areaMode=first

);



$graphTemplate['statsdflush'] = array(
		'sectiontitle' => 'statsd flush vs exception'
		,'prefixpattern' => '(^statsd)'
//		,'hostpattern' => '(.*ec2-184-73-211-106.*)'
		,'hostpattern' => '(.*)'
		,'servicepattern' => '(statsd\.graphiteStats)'
		,'suffixpattern' => '(last_flush|last_exception)'
		,'metricpatterns' => array (
			'(statsd\.graphiteStats\.last_flush)'
			,'(statsd\.graphiteStats\.last_exception)'
		)

		,'metricaliases' => array (
			'last flush',
			'last exception'
		)


		,'metricfunctions' => array (
			'derivative',
			'derivative'		)	
	// only things supported by getGraphiteDashboardHTML
		,'extraflags' => array(
		)

);


$graphTemplate['statsdstats'] = array(
		'sectiontitle' => 'statsd stats'
		,'prefixpattern' => '(^statsd)'
//		,'hostpattern' => '(.*ec2-184-73-211-106.*)'
		,'hostpattern' => '(.*)'
		,'servicepattern' => '(statsd\..*).*'
		,'suffixpattern' => '()'
		,'metricpatterns' => array (
			'(statsd\.packets_received\.counters.count)'
			,'(statsd\.bad_lines_seen\.counters.count)'
		)

		,'metricaliases' => array (
			'packets rvcd',
			'bad lines seen'
		)


		,'metricfunctions' => array (
			'secondYAxis',
			'keepLastValue'		)	
	// only things supported by getGraphiteDashboardHTML
		,'extraflags' => array(
		)

);
