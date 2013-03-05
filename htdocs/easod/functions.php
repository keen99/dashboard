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
	
	//poor mans caching - save it at least within this page load
	if ( !isset($GlobalGraphiteData) ) {
		$GlobalGraphiteData= json_decode(file_get_contents("http://$graphite_server/metrics/index.json")) ;
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
			echo "sorry, our pattern matching failed. missing hosts/services/prefixes/suffixes.. graphtemplate: dunno here. $name<br>";
			echo "pattern was /$prefixpattern\.$hostpattern.*\.$servicepattern\.$suffixpattern/<br>";			
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

function createGraphsFromTemplates($name, $orderby="service", $sumgraphs=false,$aggregate=false) {
	global $graphTemplate, $COLORS, $graphs;
	global $graphs, $metrics, $templatecolors, $graphtitle, $graphhost, $graphservice, $colors;
	global $hostpattern, $servicepattern, $graphtitle, $sectiontitle,$graphalias;
	global $metricserieslist, $metricpatterns,$metricaliases,$metricfunctions;
	
//	$leftaxisseries, $rightaxisseries, $leftaxisalias,$rightaxisalias, $leftaxisfunctions, $rightaxisfunctions;


	$debuggraph=false;
//	$debuggraph=true;
	
	
	if (! is_array($graphTemplate["$name"]) ) {
		echo "sorry, can't find graph template named $name<br>";
		exit;
	} else {

		$data=fetchGraphiteData();
printTimer('postFetch');

//read in our template//

		// our core pattern matching setup - use all 4 to find our metric key.	
		$prefixpattern=$graphTemplate["$name"]['prefixpattern'];
		$hostpattern=$graphTemplate["$name"]['hostpattern']; 		// this is the..well.host.
		$servicepattern=$graphTemplate["$name"]['servicepattern'];  //this is what gets graphed
		$suffixpattern=$graphTemplate["$name"]['suffixpattern'];

		// these two allow us to split $servicepattern matches and place them on 
		// the two Y axis
//mmmmm...
		$metricpatterns = isset($graphTemplate["$name"]['metricpatterns']) ? $graphTemplate["$name"]['metricpatterns']:array($graphTemplate["$name"]['servicepattern']);


		$leftaxispattern = isset($graphTemplate["$name"]['leftaxispattern']) ? $graphTemplate["$name"]['leftaxispattern']:array($graphTemplate["$name"]['servicepattern']);
		$rightaxispattern = isset($graphTemplate["$name"]['rightaxispattern']) ? $graphTemplate["$name"]['rightaxispattern']:array();
		// extra alias info
//mm
		$metricaliases = isset($graphTemplate["$name"]['metricaliases']) ? $graphTemplate["$name"]['metricaliases']:"";


		$leftaxisalias = isset($graphTemplate["$name"]['leftaxisalias']) ? $graphTemplate["$name"]['leftaxisalias']:"";
		$rightaxisalias = isset($graphTemplate["$name"]['rightaxisalias']) ? $graphTemplate["$name"]['rightaxisalias']:"";
		//comma seperated list of extra functions to apply...
		// no spaces!
		// and these only apply inside the alias(
//ug...
		$metricfunctions = isset($graphTemplate["$name"]['metricfunctions']) ? $graphTemplate["$name"]['metricfunctions']:array();
//		explode(",",$graphTemplate["$name"]['metricfunctions']):"";


		$leftaxisfunctions = isset($graphTemplate["$name"]['leftaxisfunctions']) ? explode(",",$graphTemplate["$name"]['leftaxisfunctions']):"";
		$rightaxisfunctions = isset($graphTemplate["$name"]['rightaxisfunctions']) ? explode(",",$graphTemplate["$name"]['rightaxisfunctions']):"";

		$templatecolors = isset($graphTemplate["$name"]['colors']) ? $graphTemplate["$name"]['colors']:"";
		

		$sectiontitle=$graphTemplate["$name"]['sectiontitle'];



		//when $sumgraphs = true (well, nonempty/nonfalse)
		//with this set to anything, we create a series list of the matching metrics for each y axis
		//then sumSeries that list
// done with template
///

		// one graph per service or host, so our outer loops loops on these
		//find our list of hosts or services
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
			if ( $orderby === "host" ) {
			//returns list of services
				$graphdata=filterData($data,$prefixpattern,$hostpattern,$servicepattern,$suffixpattern,"service");
			} elseif ( $orderby === "service" ) {
			//returns list of hosts
				$graphdata=filterData($data,$prefixpattern,$hostpattern,$servicepattern,$suffixpattern,"host");
			} else {
				echo "0unknown groupby $orderby";
				return 1;
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
				$matches = preg_grep("/$prefixpattern\.$groupid.*\.$graphid\.$suffixpattern/", $data);
			} elseif ( $orderby === "service" ) { 
				$matches = preg_grep("/$prefixpattern\.$graphid.*\.$groupid\.$suffixpattern/", $data);				
			} else {
				echo "unknown groupby $orderby";
				return 1;
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

					/*				
					echo "<pre>";
					echo "$value<br>";
					print_r($graphdata);
					echo "</pre>";
					*/
					
					if (! isset($graphdata[0]) ) {
						echo "we failed to set graphdata, what happened...value $value<br>";
					}
					$graphtarget=$graphdata[0];
					$graphprefix=$graphdata[1];
					$graphhost=$graphdata[2];
					$graphservice=$graphdata[3];
					$graphsuffix=$graphdata[4];


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

					if ( $sumgraphs ) {
					// if we ARE going to sum graphs
						if ( isset($metricpatterns[0]) ) {
							$metricid=0;
							foreach ( $metricpatterns as $metricpattern ) {
								if ( !empty($metricpattern) ) {
//echo "testing $value for $metricpattern<Br>";
									 if ( preg_match("/.*$metricpattern.*/", $value) ) {
										if (! isset($metricserieslist[$metricid]) )
											$metricserieslist[$metricid] = "";
										if (! empty($metricserieslist[$metricid]) ) 
											$metricserieslist[$metricid] .= ",";
										$metricserieslist[$metricid] .= $value ;
//echo "now series is $metricserieslist[$metricid]<br>";
									} else {
										//this is OK, it just means we didn't match THIS pattern
										//echo "HELP we didn't match a pattern2 - $value<br>";
									}
								} else {
								//this should be an ok case.
									echo "empty metric pattern for $metricid..<br>";
								}
//echo "metric id was $metricid and $metricserieslist[$metricid]<br>"; 
								$metricid++;
							}
						} else {
	//what do we do in this case..aka single metric mode
							echo "BROKE. no metric patterns..<br>";
						
						}


					} else { 
					// we're not summing

						if ( isset($metricpatterns[0]) ) {
							$metricid=0;
//find a way to create a GRAPH for each match in here..
//that could make this a bit safer for unruley metricpatterns that are too broad..then you'd just get more graphs
							foreach ( $metricpatterns as $metricpattern ) {
								if ( !empty($metricpattern) ) {
									 if ( preg_match("/.*$metricpattern.*/", $value) ) {
										if (!empty($metricaliases[$metricid]) ) 
											$graphalias = $graphalias . " - " . $metricaliases[$metricid];
									
										$metricprefix = "";
										$metricsuffix = "";

										if ( !empty($metricfunctions[$metricid]) ) {
											foreach ( explode(',',$metricfunctions[$metricid]) as $function) {
												$metricprefix .= $function . "(";
												$metricsuffix .= ")";
											}	
										}
										$whatmetric=count($metrics);
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
		echo "<hr>Final graph array:<br>";
		echo "<pre>";
		print_r($graphs);
	//	print_r($colors);
		echo "</pre>";
		echo "<hr>";
	}

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
}



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
	while ( $ii <= count($metrics)-1 ) {
	// if we don't define a set of colors to use, use our global set
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


		
		
