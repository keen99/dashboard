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


// TODO...  enable/disable/set/unset for sum and aggregate options


// diamond.iad.graphite01.cpu.cpu0.idle
$graphTemplate['cputoy1'] = array(
                'sectiontitle' => 'cpu toy'
                ,'prefixpattern' => '(^diamond\.iad)'
                ,'hostpattern' => '(.*)'
                ,'servicepattern' => 'cpu\.(.*)'
                ,'suffixpattern' => '(idle|system)'
                ,'metricpatterns' => array (
                        '(cpu\..*\.idle)'
                        ,'(cpu\..*\.system)'
                )

                ,'metricaliases' => array (
//theres something broken in aliases land
//we got idle twice...                	
				"idle",
				"system"

                )

                ,'metricfunctions' => array (
                       	'keepLastValue',
                       	'stacked,keepLastValue'
                )

);




// NO sum, aggregate prefered
$graphTemplate['cputotal'] = array(
		'sectiontitle' => 'cpu total'
		,'prefixpattern' => '(^diamond\.iad)'
		,'hostpattern' => '(.*)'
//		,'servicepattern' => 'cpu\.(.*)'
		,'servicepattern' => 'cpu\.(total)'
//		,'suffixpattern' => '(.*)'
        ,'suffixpattern' => '(user|system|steal|softirq|nice|irq|iowait|guest)'
		,'metricpatterns' => array (
			'(cpu\..*\.user)'
			,'(cpu\..*\.system)'
			,'(cpu\..*\.steal)'
			,'(cpu\..*\.softirq)'
			,'(cpu\..*\.nice)'
			,'(cpu\..*\.irq)'
			,'(cpu\..*\.iowait)'
			,'(cpu\..*\.guest)'
		)


		,'metricaliases' => array (
"user",
"system",
"steal",
"softirq",
"nice",
"irq",
"iowait",
"guest",
		)

		,'metricfunctions' => array (
			'stacked,keepLastValue'
			,'stacked,keepLastValue'
			,'stacked,keepLastValue'
			,'stacked,keepLastValue'
			,'stacked,keepLastValue'
			,'stacked,keepLastValue'
			,'stacked,keepLastValue'
			,'stacked,keepLastValue'
			,'stacked,keepLastValue'
		)



		,'metricfunctions' => array (
//			'secondYAxis',
			'keepLastValue'		)
	// only things supported by getGraphiteDashboardHTML
		 ,'extraflags' => array(
			'area_mode' => 'stacked',

// there's a color bug here, we're not landing right
//...if you use a named color that isnt in the list, we should alert

//also, this gets used BACKWARDS vs the metrics set..no, thats not it either
  		  'colors' => array (

//"user",
			'green',
//"system",
			'red',
//"steal",
			'pink',
//"softirq",
			'blue',
//"nice",
			'lightblue',
//"irq",
			'black',
//"iowait",
			'yellow',
//"guest",
			'grey'
		  )

		)

);




$graphTemplate['cpuidle'] = array(
		'sectiontitle' => 'cpu idle'
		,'prefixpattern' => '(^diamond\.iad)'
		,'hostpattern' => '(.*)'
//		,'servicepattern' => 'cpu\.(.*)'
		,'servicepattern' => 'cpu\.(total)'
//		,'suffixpattern' => '(.*)'
		,'suffixpattern' => '(idle)'
		,'metricpatterns' => array (
			'(cpu\..*\.idle)'
		)


		,'metricaliases' => array (
"idle",
		)

		,'metricfunctions' => array (
			'stacked,keepLastValue'
		)

		,'metricfunctions' => array (
//			'secondYAxis',
			'keepLastValue'		)
	// only things supported by getGraphiteDashboardHTML
		 ,'extraflags' => array(
//			'area_mode' => 'stacked',
  		  'colors' => array (
        'light-blue',

		  )
		)

);

