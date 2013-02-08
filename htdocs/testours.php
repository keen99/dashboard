<?php
require_once 'phplib/Dashboard.php';

/** the title used for the page */
$title = 'yo graphite';

/** a short alphanumeric string (used for CSS) */
//$namespace = 'chef';


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





$data = json_decode(file_get_contents("http://$graphite_server/metrics/index.json"));
//$matches = preg_grep("/.*kiwi.*rpc.*count.counters.*/", $data);
//$matches = preg_grep("/.*kiwi-app00.*rpc\.batch\.count\.counters.*/", $data);
//$data = preg_grep("/^statsd.*kiwi.*/", $data);


//kiwi rpcs
$prefixpattern="^.*";
$hostpattern=".*\.kiwi";
$servicepattern="rpc.*";
$suffixpattern="count\.counters\.count";


//collectd cpu
$prefixpattern="^collectd";
$hostpattern=".*";
$servicepattern="cpu.*";
$suffixpattern=".*";





//what hosts and rpcs do we have
$allhosts = array();
$allservices = array();
$allprefixes = array();
$allsuffixes = array();
echo "<pre>";

$p=0;
$h=0;
$s=0;
$ss=0;
foreach ( $data as $key => $value ) {
//	preg_match("/(".$prefixpattern.")(".$hostpattern.")(".$servicepattern.")(".$suffixpattern.")/", $value, $match);

//echo "value $value key $key<br>";
//echo "/($prefixpattern)($hostpattern)($servicepattern)($suffixpattern)/<br>";
	preg_match("/($prefixpattern)\.($hostpattern).*\.($servicepattern)\.($suffixpattern)/", $value, $match);

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
$allhosts = array_unique($allhosts);
$allservices = array_unique($allservices);
$allprefixes = array_unique($allprefixes);
$allsuffixes = array_unique($allsuffixes);

echo "hosts<hr>";
flush();
print_r($allhosts);
echo "services<hr>";
flush();
print_r($allservices);
echo "prefixes<hr>";
flush();
print_r($allprefixes);
echo "suffixes<hr>";
flush();
print_r($allsuffixes);



//exit;

foreach ( $allservices as $key => $value) {
	
	//echo "doing $value<hr>";
	
	//$matches = preg_grep("/.*kiwi-app.*rpc\.batch\.count\.counters.count/", $data);
	$matches = preg_grep("/$servicepattern\.$suffixpattern/", $data);
	$matches = array_unique($matches);
	
	if (! isset($graphs))
	 $graphs = array();
	$i = 0;
	foreach ($matches as $value) {
//		echo "in here for $value and $i<br>";
		
		$graphtitlepattern="/($servicepattern)/";
		preg_match($graphtitlepattern, $value, $graphtitle);

		preg_match("/($hostpattern)/", $value, $host);
//		echo "in here for $value and $i - $host[1] - $graphtitle[1]<br>";
		

	
		if (! isset($colorid) )
		  $colorid = 0;
		
		if ( $colorid > count($COLORS)-1 )
		 $colorid = 0;
		
		// group graphs by host
		//	$graphs["$host[1] $graphtitle[1]"] = array(
		//        array(
		// group graphs by title
  		    $graphs["$graphtitle[1]"]["$host[1]"] = 
				array(
					'type' => 'graphite',
		            'title' => "$host[1] - $graphtitle[1]",
//					'title' => "$host[1] - $COLORS[$colorid]",
					'metrics' => array("cactiStyle(alias(keepLastValue($value), \"$graphtitle[1]\"))"),
//					'metrics' => array("cactiStyle(keepLastValue($value))"),
					'show_legend' => 1,
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
		//for group by host
		//        ), 
			);
		  $colorid++;
		  $i++;
	}
	
	echo "<pre>";
	//print_r($graphs);
	echo "</pre>";
}


include 'phplib/template.php';
exit;
$graphs = array(

    'Run Times' => array(
        array(
            'type' => 'graphite',
            'title' => 'Max Elapsed Times',
//            'metrics' => array(
//                'maxSeries(chef.runs.*.elapsed_time)',
//            ),
			'metrics' => $matches,
        ), 
    ),

);

/** actually draws the page */
include 'phplib/template.php';

