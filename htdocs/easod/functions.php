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
        'thistle' 			,
        'rosy-brown' 		,
//        'midnight-blue' 	,
//      'blue' 				 ,
        'red' 				 ,
        'pink' 				 ,
//        'black' 			,
        'yellow-green' 		,
        'lightsteelblue' 	,
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
			echo "sorry, our pattern matching failed. missing hosts/services/prefixes/suffixes.. graphtemplate $name";
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
	global $leftaxisseries, $rightaxisseries, $leftaxisalias,$rightaxisalias, $leftaxisfunctions, $rightaxisfunctions;

//	$debuggraph=true;
	$debuggraph=false;

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
		$leftaxispattern = isset($graphTemplate["$name"]['leftaxispattern']) ? $graphTemplate["$name"]['leftaxispattern']:$servicepattern=$graphTemplate["$name"]['servicepattern'];
		$rightaxispattern = isset($graphTemplate["$name"]['rightaxispattern']) ? $graphTemplate["$name"]['rightaxispattern']:"";
		// extra alias info
		$leftaxisalias = isset($graphTemplate["$name"]['leftaxisalias']) ? $graphTemplate["$name"]['leftaxisalias']:"";
		$rightaxisalias = isset($graphTemplate["$name"]['rightaxisalias']) ? $graphTemplate["$name"]['rightaxisalias']:"";
		//comma seperated list of extra functions to apply...
		// no spaces!
		// and these only apply inside the alias(
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
		// we produce a graph for each of these.
		foreach ($groupdata as $groupid) {
			
			$leftaxisseries="";
			$rightaxisseries="";
			$metrics = array();
			$i = 0;
	
			// now identify our matches we'll use later
			if ( $orderby === "host" ) {
				$matches = preg_grep("/$prefixpattern\.$groupid.*\.$servicepattern\.$suffixpattern/", $data);
			} elseif ( $orderby === "service" ) { 
				$matches = preg_grep("/$prefixpattern\.$hostpattern.*\.$groupid\.$suffixpattern/", $data);				
			} else {
				echo "unknown groupby $orderby";
				return 1;
			}

			if ( isset($matches) && count($matches) > 0)  {
				$matches = array_unique($matches);

				foreach ($matches as $value) {
					unset($graphdata);
					preg_match("/$prefixpattern\.$hostpattern.*\.$servicepattern\.$suffixpattern/", $value, $graphdata);
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

					if ( !empty($sumgraphs) ) {
					// if we ARE going to sum graphs
						if ( !empty($rightaxispattern) && preg_match("/.*$rightaxispattern.*/", $value) ) {
							if (! empty($rightaxisseries) ) 
								$rightaxisseries .= ",";
							$rightaxisseries .= $value ;
						} else {
							if (! empty($leftaxisseries) ) 
								$leftaxisseries .= ",";
							$leftaxisseries .= $value;
						}
					} else { 
					// we're not summing
						if ( !empty($rightaxispattern) && preg_match("/.*$rightaxispattern.*/", $value) ) {
							if (!empty($rightaxisalias) ) 
								$graphalias = $graphalias . " - " . $rightaxisalias;
								
							$metricprefix = "";
							$metricsuffix = "";
							if ( !empty($rightaxisfunctions) ) {
								foreach ( $rightaxisfunctions as $function) {
									$metricprefix .= $function . "(";
									$metricsuffix .= ")";
								}	
							}
//							$metrics[$i] = "cactiStyle(alias(secondYAxis(" . $metricprefix . $value . $metricsuffix . "), \"$graphalias\"))";
							$metrics[$i] = "cactiStyle(alias(" . $metricprefix . $value . $metricsuffix . ", \"$graphalias\"))";
						} else {
							if ( !empty($leftaxisalias) ) 
								$graphalias = $graphalias . " - " . $leftaxisalias;
							$metricprefix = "";
							$metricsuffix = "";
							if ( !empty($leftaxisfunctions) ) {
								foreach ( $leftaxisfunctions as $function) {
									$metricprefix .= $function . "(";
									$metricsuffix .= ")";
								}	
							}
							$metrics[$i] = "cactiStyle(alias(" . $metricprefix . $value . $metricsuffix . ", \"$graphalias\"))";
						}
					  
					}
					$i++;

					//if we're not aggregating, spit out a graph here
					if ( !$aggregate) {
						produceGraph($orderby,$sumgraphs,$aggregate);
						$metrics=array();
						$i=0;
					}	

				} //end foreach matches

				/*
				echo "metrics are <Br>";
				echo "<pre>";
				print_R($metrics);
				echo "</pre>";
				*/		
				
				if ( $aggregate ) {
					//input here if we're summing is $firstseries/$secondseries, not metrics.
					produceGraph($orderby,$sumgraphs,$aggregate);
					$metrics=array();
				}					
			
			} else {
				echo "ERROR: found no matches<br>";
			} //endif matches
		} //foreach loopdata		
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

	echo "<hr>graph count: " . count($graphs) . " sections and $graphcount graphs - groupby $orderby<br>";
//	echo "aggegate = $aggregate and sum is $sumgraphs<br>";
	echo "<hr>";
}



function produceGraph($orderby,$sumgraphs,$aggregate) {
		global $graphs, $metrics, $templatecolors, $graphtitle, $graphhost, $servicepattern, $hostpattern;
		global $leftaxisseries, $rightaxisseries, $leftaxisalias,$rightaxisalias,$sectiontitle,$graphalias,$leftaxisfunctions, $rightaxisfunctions;
		global $graphservice, $name, $colors, $COLORS;
//echo "calling productGraph for $name $sectiontitle - alias $graphalias<br>";

		if (! isset($graphs) )
			$graphs = array();	


						if ( $orderby === "host" ) {
							$graphtitle = $sectiontitle . " - $graphhost";
						} elseif ( $orderby === "service" ) { 
							$graphtitle = $sectiontitle . " - $graphservice";
						} else {
							echo "3unknown groupby $orderby";
							return 1;
						}
		
						if ( !empty($sumgraphs) ) {
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
								}
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
							}
		
							if ( !empty($leftaxisseries) ) {

								$ourgraphalias = $graphalias . " - " . $leftaxisalias;
								$metricprefix = "";
								$metricsuffix = "";
								if ( !empty($leftaxisfunctions) ) {
									foreach ( $leftaxisfunctions as $function) {
										$metricprefix .= $function . "(";
										$metricsuffix .= ")";
									}	
								}
								$whatmetric=count($metrics);
								$metrics[$whatmetric] = "cactiStyle(alias(" . $metricprefix . "sumSeries(" . $leftaxisseries . ")" . $metricsuffix . ", \"$ourgraphalias\"))";
							}
		//we should probably hide the first axis, or only put the second on the second if there wasn't a first...
		// requires new graphite feature though
							if ( !empty($rightaxisseries) ) {

								$ourgraphalias = $graphalias . " - " . $rightaxisalias;
								$metricprefix = "";
								$metricsuffix = "";
								if ( !empty($rightaxisfunctions) ) {
									foreach ( $rightaxisfunctions as $function) {
										$metricprefix .= $function . "(";
										$metricsuffix .= ")";
									}	
								}
								$whatmetric=count($metrics);
//								$metrics[$whatmetric] = "cactiStyle(alias(secondYAxis(" . $metricprefix . "sumSeries(" . $rightaxisseries . ")" . $metricsuffix . "), \"$ourgraphalias\"))";
								$metrics[$whatmetric] = "cactiStyle(alias(" . $metricprefix . "sumSeries(" . $rightaxisseries . ")" . $metricsuffix . ", \"$ourgraphalias\"))";
		
							}
							
						}
					

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
					//			'width' => '200',
					//			'area_mode' => 'first',
					//			'line_mode' => 'connected',
					//			'line_mode' => 'slope',
					//			'line_mode' => 'staircase',
					//			'vtitle' => 'vtitle is not fun',
					//			'is_ajax' => 1,
					//			'is_pie_chart' => 1,
						);
					
					$debuggraph=false;
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

						}


} //end func produce graph
					
