<?php

require_once "phplib/globals.php";
require_once "easod/wildcard-pattern-creator.php";


// setup stuff

$COLORS = array(
        'green' ,		
        'yellow' ,			
        'cadet-blue' 		,
        'orange' 			,
//        'purple' 			,
        'gray' 				 ,
        'olive' 			,
        'light-blue' 		,
        'medium-spring-green', 
        'medium-purple' 	,
        'fire-brick' 		,
        'cornflower-blue' 	,
        'light-purple' 		,
 //       'dark-green' 		,
//      'dark-red' 			,
//        'dark-olive-green' 	,
        'deep-pink' 		,
        'light-salmon' 		,
        'lime-green' 		,
        'brown' 			,
        'pale-green' 		,
        'orange-red' 		,
      'pink' 				 ,
      'plum' 				 ,
      'tan' 				 ,
        'wheat' 			,
        'red' 				 ,
        'thistle' 			,
//        'midnight-blue' 	,
//      'blue' 				 ,
        'pink' 				 ,
//        'black' 			,
        'rosy-brown' 		,
        'lightsteelblue' 	,
        'yellow-green' 		,
        'steelblue' 		,
);
    


function startTimer() {
	global $starttime, $lasttime;
  $mtime = microtime(); 
  $mtime = explode(' ', $mtime); 
  $mtime = $mtime[1] + $mtime[0]; 
  $lasttime = $mtime;
  $starttime = $mtime; 

}


function printTimer($prefix='Timer') {
	global $starttime, $lasttime;
	if (!empty($starttime)) {
		
		$mtime = microtime(); 
		$mtime = explode(" ", $mtime);
		$mtime = $mtime[1] + $mtime[0]; 
		$endtime = $mtime; 
		$totaltime = ($endtime - $starttime); 
		$spenttime = ($endtime - $lasttime); 
		echo $prefix . ' Elapsed time: ' .$totaltime. ' seconds, ' . $spenttime . ' since last time<br>';
		flush();
		ob_flush();
		$lasttime = $mtime;
	}
}

function fetchGraphiteData() {
//todo - add some caching here
	global $graphite_server, $GlobalGraphiteData;



// theres some entertaining race conditions in here if we get hit while fetching


	//poor mans caching - save it at least within this page load
	if ( !isset($GlobalGraphiteData) ) {

// really ugly, but it's 10M at LS! we can't keep fetching that at 30s each
//need to do some expire testing and stuff.
		//fetch if 2 hours old (7200 seconds)
		if ( !file_exists ('/tmp/.dashboard.json') || time()-filemtime('/tmp/.dashboard.json') > 2 * 3600 ) {

			//the flushes in printtimer dont help us here. sigh.
			printTimer("Fetching new data, no cache found or older than 2 hours<br>");
			flush();

//and get a real tmpdir and and nan
			file_put_contents('/tmp/.dashboard.json', file_get_contents("http://$graphite_server/metrics/index.json") );
			printTimer("Finished fetching index.json");			

		} else {

			#....

			echo "using cached data - age: ";
			echo time()-filemtime('/tmp/.dashboard.json');
			echo " seconds<br>";

		}

		$GlobalGraphiteData= json_decode(file_get_contents("/tmp/.dashboard.json"));
		// $GlobalGraphiteData= json_decode(file_get_contents("http://$graphite_server/metrics/index.json")) ;
	}	
	return($GlobalGraphiteData);
}




//// half implemented logic for searching for data...
function filterData($data,$prefixpattern,$hostpattern,$servicepattern,$suffixpattern,$filter="all") {
		$p=0;
		$h=0;
		$s=0;
		$ss=0;
		foreach ( $data as $key => $value ) {
			preg_match("/$prefixpattern\.$hostpattern.*\.$servicepattern\.$suffixpattern/", $value, $match);
		
		//print_r($match);
		//exit;
		
			if ( isset($match[1]) ) {
				$allprefixes[$p] = $match[1];
				$p++;
			}
				if ( isset($match[2]) ) {
				$allhosts[$h] = $match[2];
				$h++;
			}
		
			if ( isset($match[3]) ) {
				$allservices[$s] = $match[3];
				$s++;
			}
			if ( isset($match[4]) ) {
				$allsuffixes[$ss] = $match[4];
				$ss++;
			}
		}
		if ( isset($allhosts) && isset($allservices)  && isset($allprefixes) && isset($allsuffixes) ) {
			$allhosts = array_unique($allhosts);
			$allservices = array_unique($allservices);
			$allprefixes = array_unique($allprefixes);
			$allsuffixes = array_unique($allsuffixes);
		} else {
//name isnt define here in this case..
			echo "sorry, our pattern matching failed. missing hosts/services/prefixes/suffixes.. graphtemplate: dunno here. $name<br>";
			echo "pattern was /$prefixpattern\.$hostpattern.*\.$servicepattern\.$suffixpattern/<br>";			

			print_r($match);

			return 1;
		}	

		if ( "$filter" === "all" ) {
 			return array($allhosts,$allservices,$allprefixes,$allsuffixes);
		} elseif ("$filter" === "host" ) {
			return $allhosts;
		} elseif ("$filter" === "service" ) {
			return $allservices;
		} else {
			echo "filter failure, can't filter on $filter<br>";
			return 1;
		}


}

////







//1
//  group by service, each service and each host on it's own graph - err/count split
//createGraphsFromTemplates("test template bypass", "service", false, false);
//2
//  group by service, aggregate each service (all hosts) onto one graph
//createGraphsFromTemplates("test template bypass", "service", false, true);
//5
//  group by service, each service and each host on it's own graph - err/count combined and sum
//createGraphsFromTemplates("test template bypass", "service", true, false);
//6
//  group by service, aggregate each service (all hosts) onto one graph and sum
//createGraphsFromTemplates("test template bypass", "service", true, true);

//3
//  group by host, each service and each host on it's own graph - split err/count
//createGraphsFromTemplates("test template bypass", "host", false, false);
//4
//  group by host, aggregate each host (all services) onto one graph
//createGraphsFromTemplates("test template bypass", "host", false, true);
//7
//  group by host, each service and each host on it's own graph - combined errr/count
//createGraphsFromTemplates("test template bypass", "host", true, false);
//8
//  group by host, aggregate each host (all services) onto one graph
//createGraphsFromTemplates("test template bypass", "host", true, true);


// TODO - lefty/righty becomes an array of emtrics and their stuff...




// ok ,start with one graph per item per host using metric array.



function createGraphsFromTemplates($name, $orderby="service", $sumgraphs=false,$aggregate=false) {
	global $graphTemplate, $COLORS, $graphs;
	global $graphs, $metrics, $templatecolors, $graphtitle, $graphhost, $graphservice, $colors;
	global $hostpattern, $servicepattern, $graphtitle, $sectiontitle,$graphalias;
	global $metricserieslist, $metricpatterns,$metricaliases,$metricfunctions;
	
//	$leftaxisseries, $rightaxisseries, $leftaxisalias,$rightaxisalias, $leftaxisfunctions, $rightaxisfunctions;


	$debuggraph=false;
	$debuggraph=true;
	
//we could just if/exit here. we dont need the else.
	if (! is_array($graphTemplate["$name"]) ) {
		echo "sorry, can't find graph template named $name<br>";
		exit;
	} else {

		$data=fetchGraphiteData();
//printTimer('postFetch');

//read in our template//

		// our core pattern matching setup - use all 4 to find our metric key.	
		$prefixpattern=$graphTemplate["$name"]['prefixpattern'];
		$hostpattern=$graphTemplate["$name"]['hostpattern']; 		// this is the..well.host.
		$servicepattern=$graphTemplate["$name"]['servicepattern'];  //this is what gets graphed
		$suffixpattern=$graphTemplate["$name"]['suffixpattern'];


// new shit.

		$metric = $graphTemplate[$name]['metric'];

// echo "<pre>";
// print_r($metric);
// echo "</pre>";
// exit;


		// random set of colors, if defined
		$templatecolors = isset($graphTemplate["$name"]['colors']) ? $graphTemplate["$name"]['colors']:"";
		

		$sectiontitle=$graphTemplate["$name"]['sectiontitle'];



		//when $sumgraphs = true (well, nonempty/nonfalse)
		//with this set to anything, we create a series list of the matching metrics for each y axis
		//then sumSeries that list
// done with template
///

		// one graph per service or host, so our outer loops loops on these
		//find our list of hosts or services
//if filter fails, we need to handle that instead of moving on
		$groupdata=filterData($data,$prefixpattern,$hostpattern,$servicepattern,$suffixpattern,$orderby);
		//lets pre-filter our data.  no need to retain the whole set for searching later! 
		// saves huge amounts of time for later preg_matching
		$data = preg_grep("/$prefixpattern\.$hostpattern.*\.$servicepattern\.$suffixpattern/", $data);


		if ( $debuggraph ) {	
			echo "<hr>";
			echo "Sort/group our graphs by: $orderby<br>";
			echo "<pre>";
			print_r($groupdata);
			echo "</pre>";
		}

		// loop across list of services or hosts
		// we produce a SECTION for each of these.
		foreach ($groupdata as $groupid) {
			//...unless we want an upper level agg, reset the metric counters		
			$i=0;
			$i=0;
			$metrics = array();
			$metricserieslist=array();
			$leftaxisseries="";
			$rightaxisseries="";


			//now find our list of services or hosts - we'll do a GRAPH for each of these
			// note - we cant consolidate this because we need to filter by the -other- option (host filters by service..service by filter)
			//maybe we could built this logic in filterData instead and just pass groupby
			if ( $orderby === "host" ) {
			//returns list of services
				$graphdata=filterData($data,$prefixpattern,$hostpattern,$servicepattern,$suffixpattern,"service");
			} elseif ( $orderby === "service" ) {
			//returns list of hosts
				$graphdata=filterData($data,$prefixpattern,$hostpattern,$servicepattern,$suffixpattern,"host");
			} else {
				echo "0unknown groupby $orderby";
				exit; //return 1 if we catch it upstream
			}
			if ( $debuggraph ) {	
				echo "<hr>";
				echo "and now for our graphids<pre>";
				print_r($graphdata);
				echo "</pre>";
			}


			foreach ($graphdata as $graphid) {

				//now loop through our graphs...

				if ( !$aggregate ) {
				//reset to zero if we're not aggregating AND we're not summing.. - if we are, keep growing
					$i=0;
					$metrics = array();
					$metricserieslist=array();
					$leftaxisseries="";
					$rightaxisseries="";
				}
//echo "STARTING graphid $graphid<br>";

//what do with agg and sum	
				// find our metrics that go into this graph section - or as close as we can.
				//constrain our data to only our group/section and our graph
				// now identify our matches we'll use later
				if ( $orderby === "host" ) {
//echo "in host - /$prefixpattern\.$groupid.*\.$graphid\.$suffixpattern/";
					$matches = preg_grep("/$prefixpattern\.$groupid.*\.$graphid\.$suffixpattern/", $data);
				} elseif ( $orderby === "service" ) { 
//echo "in service - /$prefixpattern\.$graphid.*\.$groupid\.$suffixpattern/";
					$matches = preg_grep("/$prefixpattern\.$graphid.*\.$groupid\.$suffixpattern/", $data);				
				} else {
					echo "unknown groupby $orderby";
					exit; //return 1 if we catch it upstream
				}


				if ( $debuggraph ) {	
					echo "<hr>";
					echo "now our matches for $graphid<br>";
					echo "<pre>";
					print_r($matches);
					echo "</pre>";
				}

				if ( isset($matches) && count($matches) > 0)  {
					$matches = array_unique($matches);
	//THESE are are our graph LINES
					foreach ($matches as $value) {
						unset($graphdata);
						preg_match("/$prefixpattern\.$hostpattern.*\.$servicepattern\.$suffixpattern/", $value, $graphdata);
	//echo "START of foreach match for $value<Br>";

						echo "graph data<br>";			
						echo "<pre>";
						echo "$value<br>";
						print_r($graphdata);
						echo "</pre>";
						
						
						if (! isset($graphdata[0]) ) {
							echo "we failed to set graphdata, what happened...value $value<br>";
						}
						//break our found items up into chunks..
						$graphtarget=$graphdata[0];
						$graphprefix=$graphdata[1];
						$graphhost=$graphdata[2];
						$graphservice=$graphdata[3];
						$graphsuffix=$graphdata[4];


//this is our default alias setup.  doesnt seem enough?
						// figure out how we're going to setup our alias.  basically should be opposite of our
						// graph title		
						if ( $orderby === "host" ) {
							$graphalias = $graphservice;
						} elseif ( $orderby === "service" ) { 
							$graphalias = $graphhost;
						} else {
							echo "2unknown groupby $orderby";
							return 1;
						}

				echo "setup alias as $graphalias for $value<br>";


echo "here 2<br>";
						if ( $sumgraphs ) {
						// if we ARE going to sum graphs

echo "here 1<br>";
							if ( is_array($metric) ) {
								$metricid=0;

								//extract the index from the array we want
								//see http://stackoverflow.com/a/14966376/3692967
								foreach ( array_keys($metric) as $index => $label) {
									$metricval = $metric[$label];

//echo "metric loop key $key - " . $metricval['alias']. "<br>";
echo "here - $index and $label ";
// echo "<pre>";
// print_r($metricval);
// echo "</pre>";
// exit;
//continue;
									if ( !empty($metricval['pattern']) ) {


	//echo "testing $value for " . $metricval['pattern'] . "<Br>";
										 if ( preg_match("/.*" . $metricval['pattern'] . ".*/", $value) ) {
											if (! isset($metricserieslist[$index]) )
												$metricserieslist[$index] = "";
											if (! empty($metricserieslist[$index]) ) 
												$metricserieslist[$index] .= ",";
											$metricserieslist[$index] .= $value ;
	//echo "now series is $metricserieslist[$index]<br>";
										} else {
											//this is OK, it just means we didn't match THIS pattern
											//echo "HELP we didn't match a pattern2 - $value<br>";
										}
									} else {
									//this should be an ok case.
										echo "empty metric pattern for $index..<br>";
									}
	//echo "metric id was $index and $metricserieslist[$index]<br>"; 
									//$metricid++;
								}
							} else {
		//what do we do in this case..aka single metric mode
								echo "BROKE. no metric patterns..<br>";
								exit;
							}


						} else { 
						// we're not summing

echo "here 3.5<br>";

							if ( is_array($metric) ) {
//metricid should die
								$metricid=0;
	//find a way to create a GRAPH for each match in here..
	//that could make this a bit safer for unruley metricpatterns that are too broad..then you'd just get more graphs

//we loop through each metric we want to see if it matches the value we already have.  that's why our order is goofy.
//								foreach ( $metric as $key => $metricval) {

// echo "here ";
// echo "<pre>";
// print_r(array_keys($metric));
// echo "</pre>";
//exit;
								//extract the index from the array we want
								//see http://stackoverflow.com/a/14966376/3692967
								foreach ( array_keys($metric) as $index => $label) {
									$metricval = $metric[$label];

//echo "metric loop key $key - " . $metricval['alias']. "<br>";
echo "here - $index and $label ";
// echo "<pre>";
// print_r($metricval);
// echo "</pre>";
// exit;
//continue;
									if ( !empty($metricval['pattern']) ) {


										 if ( preg_match("/.*" . $metricval['pattern'] . ".*/", $value) ) {

//if we specify an alias, append it to our automatic alias.
											if (!empty($metricval['alias']) ) 
												$graphalias = $graphalias . " - " . $metricval['alias'];
											$metricprefix = "";
											$metricsuffix = "";

											if ( !empty($metricval['function']) ) {
												foreach ( explode(',',$metricval['function']) as $function) {
													$metricprefix .= $function . "(";
													$metricsuffix .= ")";
												}	
											}
	

											//for agg, we need to use the index as defined so users can control order
												if ( $aggregate ) {
												$whatmetric=$index;
											} else {
											//for everything else, just start at zero (this should only be a single entry per graph....we 
	//note that there's no order-on-page control with this... but if we index to >0 graphfactory blows up.
												$whatmetric=count($metrics);
											}

											$metrics[$whatmetric] = "cactiStyle(alias(" . $metricprefix . $value . $metricsuffix . ", \"$graphalias\"))";
										} else {
											//this is ok
											//echo "HELP we didn't match a pattern1 - $value<br>";
										}
									} else {
									//this should be an ok case.
										echo "empty metric pattern for $metricid..<br>";
									}
	//echo "and metricid is $metricid<br>";
									$metricid++;
								} //done foreach patterns
							} else {
		//what do we do in this case..aka single metric mode
								echo "BROKE. no metric patterns..<br>";
							
							}

						}
						$i++;

echo "here 3<br>";		
						//if we're not aggregating, spit out a graph here
	//graph per metric
	//					if ( !$aggregate ) {
						if ( !$aggregate && !$sumgraphs) {
	//echo "one graph per metric, go<br>";
							produceGraph($name,$orderby,$sumgraphs,$aggregate);
							$metrics=array();
							$i=0;
						}
		
	//				echo "END of foreach match for $value, is is $i<Br>";
	// 				echo "end foreach metrics are <Br>";
	// 				echo "<pre>";
	// 				print_R($metrics);
	// 				echo "</pre>";
					} //end foreach lines matches
	//when we sum
	//we dont use "metrics"
	//we use series
					if ( !$aggregate && $sumgraphs) {
	//				echo "one graph per set of metrics, go<br>";
	//echo "series are<br>";
	//echo "left $leftaxisseries<br>";
	//echo "right $rightaxisseries<r>";
						produceGraph($name,$orderby,$sumgraphs,$aggregate);
					}

	/*				
		echo "metrics are <Br>";
		echo "<pre>";
		print_R($metrics);
		echo "</pre>";
	*/
			
				
				} else {
					//it's actually valid to sometimes not find matches
					/*
					echo "ERROR: found no matches $graphid<br>";
					echo "<pre>";
					print_r($matches);
					echo "</pre>";
					*/
				} //endif matches


//echo "END graphid $graphid<br>";
			}//foreach graphdata

//onegraph per groupid
				if ( $aggregate ) {
//echo "one graph per groupid $groupid, o<br>";

					//input here if we're summing is $firstseries/$secondseries, not metrics.
					produceGraph($name,$orderby,$sumgraphs,$aggregate);
					$metrics=array();
				}	

		} //foreach groupdata		
	}//endif name

	if ( $debuggraph ) {
		echo "<hr>Final graph array [\$graphs]:<br>";
		echo "<pre>";
		print_r($graphs);
	//	print_r($colors);
		echo "</pre>";
		echo "<hr>";
	}

	//above graphs array consumed by...
	// template_content, 
	//GraphFactory::getInstance()->getDashboardSectionsHTML($graphs,
	// then we to get...section html, which creates $graph_config
	// then to $html .= $this->getDashboardHTML($graph_config);
	// into return $this->getGraphiteDashboardHTML($graph_config);


	printTimer('doneGraphs');

	$graphcount=0;
	foreach ( $graphs as $topkey => $value ) {
		foreach ( $graphs[$topkey] as $key => $value ) {
			$graphcount++;
		}
	}
	//printTimer('doneGraphCounting');

//	echo "<hr>graph count: " . count($graphs) . " sections and $graphcount graphs - groupby $orderby<br>";
//	echo "aggegate = $aggregate and sum is $sumgraphs<br>";
//	echo "<hr>";
} //end createGraphsFromTemplates


// returns/builds $graphs
function produceGraph($name,$orderby,$sumgraphs,$aggregate) {
	global $graphs, $metrics, $templatecolors, $graphtitle, $graphhost, $servicepattern, $hostpattern;
	global $sectiontitle,$graphalias;
	global $graphTemplate;

//	global $leftaxisseries, $rightaxisseries, $leftaxisalias,$rightaxisalias
// global $leftaxisfunctions, $rightaxisfunctions;
	global $metricserieslist, $metricpatterns,$metricaliases,$metricfunctions;
	global $graphservice, $colors, $COLORS;
//echo "calling productGraph for $name $sectiontitle - alias $graphalias<br>";

	$debuggraph=false;
//	$debuggraph=true;

	if (! isset($graphs) )
		$graphs = array();	

	if ( $orderby === "host" ) {
		$graphtitle = $sectiontitle . " - $graphhost";
	} elseif ( $orderby === "service" ) { 
		$graphtitle = $sectiontitle . " - $graphservice";
	} else {
		echo "3unknown groupby $orderby";
		return 1;
	} //endif orderby

	if ( $sumgraphs ) {
		//we ARE summing AND aggregating
		if ( $aggregate) {
			if ( $orderby === "host" ) {
				$graphtitle = $sectiontitle . " - $graphhost";
				$graphalias = $servicepattern;
			} elseif ( $orderby === "service" ) { 
				$graphtitle = $sectiontitle . " - $graphservice";
				$graphalias = $hostpattern;
			} else {
				echo "4unknown groupby $orderby";
				return 1;
			} //endif orderby
		} else { 
			// summing but NOT aggregating
			if ( $orderby === "host" ) {
				$graphtitle = $sectiontitle . " - $graphhost";

			} elseif ( $orderby === "service" ) { 
				$graphtitle = $sectiontitle . " - $graphservice";
			} else {
				echo "4unknown groupby $orderby";
				return 1;
			}
		} //endif aggregate


///////


		$metricid=0;
		//metricserieslist isn't ness sorted in the right order when we built it, so fix that
		ksort($metricserieslist);
		foreach ($metricserieslist as $metricseries ) {

				$metricalias = $graphalias;
				if ( isset($metricaliases[$metricid]) && !empty($metricaliases[$metricid]) ) 
					$metricalias .= " - " . $metricaliases[$metricid];
//echo "now metricid is $metricid and alias $metricalias and series $metricseries<br>";

					
				$metricprefix = "";
				$metricsuffix = "";
				if ( isset($metricfunctions[$metricid]) && !empty($metricfunctions[$metricid])) {
					foreach ( explode(',',$metricfunctions[$metricid]) as $function) {
						//if it's a function that is  "internal" to sumSeries
						if ( "$function" === "keepLastValue" ) {
							$output="";
							foreach ( explode(',', $metricseries) as $value  ) {
								if (! empty($output) ) 
									$output .= ",";						
								$output .= $function . "(" . $value . ")";	
							}						
							$metricseries=$output;
							unset($output);
	
						} else {
						//or external to sumseries
							$metricprefix .= $function . "(";
							$metricsuffix .= ")";
						}
						$metricid++;
					}	
				}
				//find out next metric to output
				$whatmetric=count($metrics);
				$metrics[$whatmetric] = "cactiStyle(alias(" . $metricprefix . "sumSeries(" . $metricseries . ")" . $metricsuffix . ", \"$metricalias\"))";
		} //end foreach metricpatterns

	} //endif sumgraphs
			

	$ii=0;
	$colors=array();
//while this isn't our problem, we're already reversed, 
//we shouldnt get in here if we have colors...	
	while ( $ii <= count($metrics)-1 ) {
	// if we don't define a set of colors to use, use our global set
//if extra>colors is set, we still get in here.
		if ( empty($templatecolors) ) {
			if (! isset($colorid) )
			//	$colorid = 0;
				$colorid = rand(0, count($COLORS)-1);				
			//prevent dup colors next to each other anyway	
			if ( isset($lastcolorid) && $lastcolorid === $colorid ) 
				$colorid++;
			if ( $colorid > count($COLORS)-1 )
			//	$colorid = 0;					
				$colorid = rand(0, count($COLORS)-1);				

				//$colorid = rand(0, count($COLORS)-1);				
// echo "what about now.<br>";
// echo "<pre>";
// print_r($metrics);
// echo "</pre>";
// echo "are we here? $COLORS[$colorid] <br>";
			$colors[$ii] = $COLORS[$colorid];
			$colorid++;
			$ii++;
		} else {
		//if we defined a list of colors, use those as our source of color - still random though.
			if (! isset($colorid) )
			//	$colorid = 0;
				$colorid = rand(0, count($templatecolors)-1);	
			//prevent dup colors next to each other anyway	
			if ( isset($lastcolorid) && $lastcolorid === $colorid ) 
				$colorid++;
			if ( $colorid > count($templatecolors)-1 )
			//	$colorid = 0;					
				$colorid = rand(0, count($templatecolors)-1);				
			$colors[$ii] = $templatecolors[$colorid];
			$lastcolorid = $colorid;
			$colorid++;
			$ii++;
print_r($colors);
		}
	}


	$graphtitle = $graphtitle . " (" . count($metrics) . ")";

//this should really double if we're wide enough..hrm...
	if ( count($metrics) > 4 ) {
		$showlegend=0;
	} else {
		$showlegend=1;
	}
	//hideLegend=false to force it on for testin

	$newgraph = 
		array(
			'type' => 'graphite',
			'title' => $graphtitle,
			'metrics' => $metrics, 
			'show_legend' => $showlegend,
			'show_html_legend' => 1,
			'show_copy_url' => 0,
//			'height' => '120',
			'colors' => $colors,
//			'width' => '435',
//			'area_mode' => 'first',
//			'line_mode' => 'connected',
//			'line_mode' => 'slope',
//			'line_mode' => 'staircase',
//		'vtitle' => 'vtitle is not fun',
//			'is_ajax' => 1,
//			'is_pie_chart' => 1,
	);
	
	if ( isset($graphTemplate["$name"]['extraflags']) && is_array($graphTemplate["$name"]['extraflags']) ) {
		foreach ( $graphTemplate["$name"]['extraflags'] as $flag => $value) {
			$newgraph[$flag] = $value;
		}
	}

	if ( $debuggraph ) {
		echo "new graph is <br>";
		echo "<pre>";
		print_r($newgraph);
		echo "</pre>";
	}

	if ( $aggregate ) {
		// this sets up the graph array - we group on-page by the first arg, and display the second arg
		if ( "$orderby" == "host" ) {
			$graphs["$sectiontitle($orderby)"]["$graphhost"] = $newgraph;

		} elseif ( "$orderby" == "service" ) {
			$graphs["$sectiontitle($orderby)"]["$graphservice"] = $newgraph;
		} else { 
			echo "5unknown groupby for this graph template - $name<br>";
			return 1;
		}
	} else {
		if ( "$orderby" == "host" ) {
			$graphs["$graphhost"]["$graphservice-$graphalias"] = $newgraph;
		} elseif ( "$orderby" == "service" ) {
			$graphs["$graphservice"]["$graphhost-$graphalias"] = $newgraph;
		} else { 
			echo "6unknown groupby for this graph template - $name<br>";
			return 1;
		}
//echo "done it to graphs - $graphservice - $graphhost - $graphalias<br>";

	}//endif agg

} //end func produce graph
		
		
		
		
		
		
// page rendering stuff

function buildGraphOptions() { 
	global $additional_controls;
	if ( !isset($additional_controls) )
		$additional_controls="";
	$additional_controls .= "<span class='control'>";
	//default to service
	if ( !isset($_GET['groupby']) ) { $_GET['groupby'] = 'service'; }
	$additional_controls .= Controls::buildRadio('groupby', 'Group By', array('service' => 'Service' , 'host' => 'Host'), $_GET['groupby'] );
	$additional_controls .= "</span><span class='control'>";
	//default to don't sum
	if (! isset($_GET['sum']) ) { $_GET['sum'] = 0; }
	$additional_controls .= Controls::buildCheckbox('sum', 'Sum Items', $_GET['sum'] );
	//default to don't agg
	if (! isset($_GET['agg']) ) { $_GET['agg'] = 0; }
	$additional_controls .= Controls::buildCheckbox('agg', 'Aggregate Graphs', $_GET['agg'] );
	$additional_controls .= '</span>';
}

function buildTemplateDropdown() {
	global $graphTemplate, $additional_controls;
	$graphTemplate['empty'] = array(
		'sectiontitle' => '-Graph Template-'
	);
	if ( !isset($additional_controls) )
		$additional_controls="";
	if ( !isset($_GET['graphtemplate']) ) {	$_GET['graphtemplate'] = 'empty'; }
	foreach ( $graphTemplate as $key => $value) { $graphTemplates[$key] = $value['sectiontitle']; }
	natcasesort($graphTemplates); // give these a nice sorted order	
	$additional_controls .= "<span class='control'>";
//	$additional_controls .= Controls::buildSelect('graphtemplate', 'Graph Template', $graphTemplates, $_GET['graphtemplate'] );
	$additional_controls .= Controls::buildSelect('graphtemplate', '', $graphTemplates, $_GET['graphtemplate'] );
	$additional_controls .= '</span>';
}

// this builds the list of 
function generateListBox($graphs) {
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


		
		
