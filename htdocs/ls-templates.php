<?php


// // these statsd templates may not be able to work w/ our namespacing here..
// // not with 4 pieces of match.

// $graphTemplate['statsdflush'] = array(
// 		'sectiontitle' => 'statsd flush vs exception'
// 		,'prefixpattern' => '(^statsd)'
// //		,'hostpattern' => '(.*ec2-184-73-211-106.*)'
// 		,'hostpattern' => '(.*)'
// 		,'servicepattern' => '(statsd\.graphiteStats)'
// 		,'suffixpattern' => '(last_flush|last_exception)'
// 		,'metricpatterns' => array (
// 			'(statsd\.graphiteStats\.last_flush)'
// 			,'(statsd\.graphiteStats\.last_exception)'
// 		)

// 		,'metricaliases' => array (
// 			'last flush',
// 			'last exception'
// 		)


// 		,'metricfunctions' => array (
// 			'derivative',
// 			'derivative'		)	
// 	// only things supported by getGraphiteDashboardHTML
// 		,'extraflags' => array(
// 		)

// );
// $graphTemplate['statsdstats'] = array(
// 		'sectiontitle' => 'statsd stats'
// 		,'prefixpattern' => '(^statsd)'
// //		,'hostpattern' => '(.*ec2-184-73-211-106.*)'
// 		,'hostpattern' => '(.*)'
// 		,'servicepattern' => '(statsd\..*).*'
// 		,'suffixpattern' => '()'
// 		,'metricpatterns' => array (
// 			'(statsd\.packets_received\.counters.count)'
// 			,'(statsd\.bad_lines_seen\.counters.count)'
// 		)

// 		,'metricaliases' => array (
// 			'packets rvcd',
// 			'bad lines seen'
// 		)


// 		,'metricfunctions' => array (
// 			'secondYAxis',
// 			'keepLastValue'		)	
// 	// only things supported by getGraphiteDashboardHTML
// 		,'extraflags' => array(
// 		)

// );


// TODO...  enable/disable/set/unset for sum and aggregate options
// for now, just user/nice/system/io - the others (and there could be more, 
// depends on kernel) should really get sumed together but we dont have tools 
// for that
// diamond.iad.graphite01.cpu.cpu0.idle
// NO sum, aggregate prefered
$graphTemplate['cputotal'] = array(
		'sectiontitle' => 'cpu total'
		,'prefixpattern' => '(^diamond\.iad)'
		,'hostpattern' => '(.*)'
//		,'servicepattern' => 'cpu\.(.*)'
//		,'servicepattern' => 'cpu\.(total)'
		,'servicepattern' => '(cpu\.total)'
//this goes a bit wild w/ service+agg..and goes uri-limit w/ host+agg
//		,'servicepattern' => '(cpu\..*)'
//		,'suffixpattern' => '(.*)'
//        ,'suffixpattern' => '(user|system|steal|softirq|nice|irq|iowait|guest)'
//this has to really be specific, otherwise some graph views get bad
        ,'suffixpattern' => '(user|system|nice|iowait)'

		,'metricpatterns' => array (
			// '(cpu\..*\.steal)',
			// '(cpu\..*\.softirq)',
			// '(cpu\..*\.irq)',
			'(cpu\..*\.iowait)',
			// '(cpu\..*\.guest)',
			'(cpu\..*\.system)',
			'(cpu\..*\.user)',
			'(cpu\..*\.nice)',
		)
		,'metricaliases' => array (
			// "steal",
			// "softirq",
			// "irq",
			"iowait",
			// "guest",
			"system",
			"user",
			"nice",
		)
		// only things supported by GraphFactory::getGraphiteDashboardHTML
		,'extraflags' => array(
			'area_mode' => 'stacked',
			'colors' => array (
				// //steal
				// 'grey',
				// //softirq
				// 'light-blue',
				//irq pink
				// 'FF00FF',
				//iowait, yellow
				'FFF200',
				// // guest,
				// 'wheat',
				// system, red
				'FF0000',
				// user, blue
				'0000FF',
				//nice, green
				'00FF00',
			)
		)

);

//dsr


//host +- agg only for this graph.
$graphTemplate['cputotal'] = array(
		'sectiontitle' => 'cpu total'
		,'prefixpattern' => '(^diamond\.iad)'
		,'hostpattern' => '(.*)'
//		,'hostpattern' => '(cache04)'

//		,'servicepattern' => 'cpu\.(.*)'
//		,'servicepattern' => 'cpu\.(total)'
		,'servicepattern' => '(cpu\.total)'
//this goes a bit wild w/ service+agg..and goes uri-limit w/ host+agg
//		,'servicepattern' => '(cpu\..*)'
//		,'suffixpattern' => '(.*)'
//        ,'suffixpattern' => '(user|system|steal|softirq|nice|irq|iowait|guest)'
//this has to really be specific, otherwise some graph views get bad
        ,'suffixpattern' => '(user|system|nice|iowait)' //|idle)'

        ,'metric' => array (
	        'iowait' => array (
	        	'pattern' => '(cpu\..*\.iowait)',
	        	'alias' => 'iowait',
	        	'color' => 'FFF200', //yellow
				'function' => 'stacked,keepLastValue'
	        )
	        ,'system' => array (
	        	'pattern' => '(cpu\..*\.system)',
	        	'alias' => 'system',
	        	'color' => 'FF0000', //red
				'function' => 'stacked,keepLastValue'
	        )	
	        ,'user' => array (
	        	'pattern' => '(cpu\..*\.user)',
	        	'alias' => 'user',
	        	'color' => '0000FF', //blue
				'function' => 'stacked,keepLastValue'
	        )
	        ,'nice' => array (
	        	'pattern' => '(cpu\..*\.nice)',
	        	'alias' => 'nice',
	        	'color' => '00FF00', //green
				'function' => 'stacked,keepLastValue'
	        )
	   //      ,'idle' => array (
	   //      	'pattern' => '(cpu\..*\.idle)',
	   //      	'alias' => 'idle',
	   //      	'color' => 'white',
				// 'function' => 'keepLastValue,secondYAxis'
	   //      )

	    )
	
		// only things supported by GraphFactory::getGraphiteDashboardHTML
//if we want aggs to stack, we NEED THIS, regardless of the other stacked
		,'extraflags' => array(
			'area_mode' => 'stacked',
		)

);

//dsr


//host only, service/agg ok but doesnt do anything. 
$graphTemplate['cpuidle'] = array(
		'sectiontitle' => 'cpu idle'
		,'prefixpattern' => '(^diamond\.iad)'
		,'hostpattern' => '(.*)'
		,'servicepattern' => '(cpu\.total)'
//		,'servicepattern' => '(cpu\..*)'
		,'suffixpattern' => '(idle)'
        ,'metric' => array (
	        'idle' => array (
	        	'pattern' => '(cpu\..*\.idle)',
	        	'alias' => 'idle',
	        	'color' => 'blue',
				'function' => 'keepLastValue'
	        )
	    )
	// only things supported by getGraphiteDashboardHTML
		,'extraflags' => array(
//			'area_mode' => 'stacked',
		)
		

);


//host+agg
// memory...that's going to be hard!
// we need to apply formula I bet...
// diamond.iad.app-cron01.memory.MemFree
$graphTemplate['memoryfree'] = array(
		'sectiontitle' => 'memory free'
		,'prefixpattern' => '(^diamond\.iad)'
		,'hostpattern' => '(.*)'
		,'servicepattern' => '(memory)'
//		,'suffixpattern' => '(.*)'
		,'suffixpattern' => '(Mem.*)'

        ,'metric' => array (
	        'free' => array (
	        	'pattern' => '(memory\.MemFree)',
	        	'alias' => 'free',
	        	'color' => 'green',
				'function' => 'stacked,keepLastValue'
	        ),
	        'total' => array (
	        	'pattern' => '(memory\.MemTotal)',
	        	'alias' => 'total',
	        	'color' => 'blue',
				'function' => 'keepLastValue'
	        ),
	   //      'active' => array (
	   //      	'pattern' => '(memory\.Active)',
	   //      	'alias' => 'active',
	   //      	'color' => 'white',
				// 'function' => 'keepLastValue'
	   //      ),
	    )

			// ,'(memory\.Active)'
			// ,'(memory\.Buffers)'
			// ,'(memory\.Cached)'
			// ,'(memory\.Dirty)'
			// ,'(memory\.Inactive)'


);




// diamond.iad.app-cron01.sockets.used
$graphTemplate['socketsused'] = array(
		'sectiontitle' => 'sockets used'
		,'prefixpattern' => '(^diamond\.iad)'
		,'hostpattern' => '(.*)'
		,'servicepattern' => '(sockets)'
		,'suffixpattern' => '(used)'
        ,'metric' => array (
	        'used' => array (
	        	'pattern' => '(sockets\.used)',
	        	'alias' => 'used',
//	        	'color' => 'green',
//				'function' => 'keepLastValue'
	        ),
	    ),

);






// diamond.iad.app-cron01.loadavg.01
$graphTemplate['loadavg'] = array(
		'sectiontitle' => 'load average'
		,'prefixpattern' => '(^diamond\.iad)'
		,'hostpattern' => '(.*)'
		,'servicepattern' => '(loadavg)'
		,'suffixpattern' => '(01|05|15)'
		,'metricpatterns' => array (
			'(loadavg.01)',
			'(loadavg.05)',
			'(loadavg.15)',
		)

		,'metricaliases' => array (
			"1 min loadavg",
			"5 min loadavg",
			"15 min loadavg",

		)
);
