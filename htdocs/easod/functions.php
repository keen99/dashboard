<?php

require_once "phplib/globals.php";


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
        'wheat' 			,
        'tan' 				 ,
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
	global $starttime;
  $mtime = microtime(); 
  $mtime = explode(' ', $mtime); 
  $mtime = $mtime[1] + $mtime[0]; 
  $starttime = $mtime; 

}


function printTimer($prefix='Timer') {
	global $starttime;
  $mtime = microtime(); 
  $mtime = explode(" ", $mtime);
  $mtime = $mtime[1] + $mtime[0]; 
  $endtime = $mtime; 
  $totaltime = ($endtime - $starttime); 
  echo $prefix . ' Elapsed time: ' .$totaltime. ' seconds<br>';
}

function fetchGraphiteData() {
//todo - add some caching here
	global $graphite_server;
	return( json_decode(file_get_contents("http://$graphite_server/metrics/index.json")) );
}




// this is how we define our templates.
// usage:  addGraphTemplate( array(.....));
function addGraphTemplate($name, $sectiontitle, $prefixpattern, $hostpattern, $servicepattern, $suffixpattern, $metrics=NULL) {
	global $graphTemplate;



	if (! isset($graphTemplates) )
	 $graphTemplates = array();
 
	$templateid = count($graphTemplates) + 1;


	$graphTemplate["$name"] = array(
		'type' => 'graphite',
		'sectiontitle' => "$sectiontitle",
		'prefixpattern' => "$prefixpattern",
		'hostpattern' => "$hostpattern",	
		'servicepattern' => "$servicepattern",		
		'suffixpattern' => "$suffixpattern",
		'metrics' => $metrics
	);


}

// goal: output a section of graphs from a graph template
function createGraphsFromTemplates($name, $groupby="service") {
	global $graphTemplate, $COLORS, $graphs; 


	if (! is_array($graphTemplate["$name"]) ) {
		echo "sorry, can't find graph template named $name<br>";
		exit;
	} else {

		$data=fetchGraphiteData();
	
		$prefixpattern=$graphTemplate["$name"]['prefixpattern'];
		$hostpattern=$graphTemplate["$name"]['hostpattern'];
		$servicepattern=$graphTemplate["$name"]['servicepattern'];
		$suffixpattern=$graphTemplate["$name"]['suffixpattern'];
	
	
		$matches = preg_grep("/$prefixpattern\.$hostpattern.*\.$servicepattern\.$suffixpattern/", $data);
		$matches = array_unique($matches);
		
		if (! isset($graphs) )
		 $graphs = array();
	
		$i = 0;
		foreach ($matches as $value) {
			
			//		echo "in here for $value and $i<br>";
			unset($graphdata);
			preg_match("/$prefixpattern\.$hostpattern.*\.$servicepattern\.$suffixpattern/", $value, $graphdata);
//echo "<pre>";
//print_r($graphdata);
			if (! isset($graphdata[0]) ) {
			 echo "we failed to set graphdata, what happened...value $value<br>";
			}
			$graphtarget=$graphdata[0];
			$graphprefix=$graphdata[1];
			$graphhost=$graphdata[2];
			$graphservice=$graphdata[3];
			$graphsuffix=$graphdata[4];





			if (! isset($colorid) )
			  $colorid = 0;
			
			if ( $colorid > count($COLORS)-1 )
			 $colorid = 0;					

			if ( is_array($graphTemplate["$name"]["metrics"]) ) {
				$metrics = $graphTemplate["$name"]["metrics"];
			} else {
				$metrics = array("cactiStyle(alias(keepLastValue($value), \"$graphservice\"))");
			}
			$newgraph = 
				array(
					'type' => 'graphite',
					'title' => "$graphhost - $graphservice",
					'metrics' => $metrics, 
					'show_legend' => 0,
					'show_html_legend' => 1,
					'show_copy_url' => 0,
					'height' => '120',
					'color' => $COLORS[$colorid],
		//			'width' => '200',
		//			'area_mode' => 'first',
		//			'line_mode' => 'connected',
		//			'line_mode' => 'slope',
		//			'line_mode' => 'staircase',
		//			'vtitle' => 'vtitle is not fun',
		//			'is_ajax' => 1,
		//			'is_pie_chart' => 1,
			);

			if ( "$groupby" == "host" ) {
				$graphs["$graphhost"]["$graphservice"] = $newgraph;
			} elseif ( "$groupby" == "service" ) {
				$graphs["$graphservice"]["$graphhost"] = $newgraph;
			} else { 
				echo "unknown groupby for this graph template - $name";
				return 1;
			}

			$colorid++;
			$i++;
		}
	}
	/*
	echo "<pre>";
	print_r($graphs);
	echo "</pre>";
	*/
}




//// half implemented logic for searching for data...
function MagicData() {
	
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
	
}

////






function createGraphsFromTemplatesAggregate($name, $groupby="service") {
	global $graphTemplate, $COLORS, $graphs; 


	if (! is_array($graphTemplate["$name"]) ) {
		echo "sorry, can't find graph template named $name<br>";
		exit;
	} else {

		$data=fetchGraphiteData();

printTimer('postFetch');
	
		$prefixpattern=$graphTemplate["$name"]['prefixpattern'];
		$hostpattern=$graphTemplate["$name"]['hostpattern'];
		$servicepattern=$graphTemplate["$name"]['servicepattern'];
		$suffixpattern=$graphTemplate["$name"]['suffixpattern'];
	
	
	
		$p=0;
		$h=0;
		$s=0;
		$ss=0;


			$matches = preg_grep("/$prefixpattern\.$hostpattern.*\.$servicepattern\.$suffixpattern/", $data);
			$matches = array_unique($matches);
			
			if (! isset($graphs) )
			 $graphs = array();
		
			$i = 0;
			//for each 
			foreach ($matches as $value) {
				
				//		echo "in here for $value and $i<br>";
				unset($graphdata);
				preg_match("/$prefixpattern\.$hostpattern.*\.$servicepattern\.$suffixpattern/", $value, $graphdata);
//echo "<pre>";
//print_r($graphdata);
				if (! isset($graphdata[0]) ) {
				 echo "we failed to set graphdata, what happened...value $value<br>";
				}
				$graphtarget=$graphdata[0];
				$graphprefix=$graphdata[1];
				$graphhost=$graphdata[2];
				$graphservice=$graphdata[3];
				$graphsuffix=$graphdata[4];





				if (! isset($colorid) )
				  $colorid = 0;
				
				if ( $colorid > count($COLORS)-1 )
				 $colorid = 0;					

				if ( is_array($graphTemplate["$name"]["metrics"]) ) {
					$metrics = $graphTemplate["$name"]["metrics"];
				} else {
					$metrics = array("cactiStyle(alias(keepLastValue($value), \"$graphservice\"))");
				}
				$newgraph = 
					array(
						'type' => 'graphite',
			            'title' => "$graphhost - $graphservice",
						'metrics' => $metrics, 
						'show_legend' => 0,
						'show_html_legend' => 1,
						'show_copy_url' => 0,
						'height' => '120',
						'color' => $COLORS[$colorid],
			//			'width' => '200',
			//			'area_mode' => 'first',
			//			'line_mode' => 'connected',
			//			'line_mode' => 'slope',
			//			'line_mode' => 'staircase',
			//			'vtitle' => 'vtitle is not fun',
			//			'is_ajax' => 1,
			//			'is_pie_chart' => 1,
				);

				if ( "$groupby" == "host" ) {
					$graphs["$graphhost"]["$graphservice"] = $newgraph;
				} elseif ( "$groupby" == "service" ) {
					$graphs["$graphservice"]["$graphhost"] = $newgraph;
				} else { 
					echo "unknown groupby for this graph template - $name";
					return 1;
				}

				$colorid++;
				$i++;
			}
printTimer('postServices');
	}
	/*
	echo "<pre>";
	print_r($graphs);
	echo "</pre>";
	*/

	echo "<hr>graph count: " . count($graphs) . "<hr><br>";

}

