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

//what hosts and rpcs do we have
$allhosts = array();
$allrpcs = array();
$i=0;
$y=0;
foreach ( $data as $key => $value ) {
	preg_match("/statsd\.(.*)\.kiwi.*\.(rpc.*)\.count\.counter.*/", $value, $match);

	if ( isset($match[1]) ) {
		$allhosts[$i] = $match[1];
		$i++;
	}
	if ( isset($match[2]) ) {
		$allrpcs[$y] = $match[2];
		$y++;
	}
}
$allhosts = array_unique($allhosts);
$allrpcs = array_unique($allrpcs);

echo "<pre>";
//print_r($data);
//print_r($allhosts);
//print_r($allrpcs);


foreach ( $allrpcs as $key => $value) {

//echo "doing $value<hr>";

//$matches = preg_grep("/.*kiwi-app.*rpc\.batch\.count\.counters.count/", $data);
$matches = preg_grep("/.*$value\.count\.counters.count/", $data);


//echo "we are here<br>";
//echo "<pre>";
//print_r($data);
//print_r($matches);


//exit;
//now restructure to support how we want to graph it
//reorder keys to be 0++ as required
$i = 0;
foreach ($matches as $key => $value) {	  
	unset($matches[$key]);
    $matches[$i] = $value;
//    $matches[$i] = 'keepLastValue(' . $value . ')';
	$i++;
}  

//print_r($matches);
//echo "<br>";
echo "</pre>";

//exit;




$firstgraph =  array(
'sumSeries(keepLastValue(statsd.live-kiwi-app*.kiwi.9000.rpc.auction.bid.count.counters.count))' ,
'sumSeries(keepLastValue(statsd.live-kiwi-app*.kiwi.9000.rpc.auction.bid.errors.counters.count))');


/*
?target=color(alias(sumSeries(keepLastValue(
statsd.live-kiwi-app*.kiwi.9000.rpc.auction.bid.count.counters.count))
%20%2C"count")%2C"blue")&target=color(
alias(stacked(sumSeries(keepLastValue(
statsd.live-kiwi-app*.kiwi.9000.rpc.auction.bid.errors.counters.count))
)%2C"errors")%2C"red")
&width=500&height=200&title=kiwi%20rpc%20auction%20bid%20count/errors&from=-1days&

*/

/** sections and graphs to be shown on the page */

if (! isset($graphs))
 $graphs = array();
$i = 0;
foreach ($matches as $value) {
//echo "in here for $value and $i<br>";


$graphtitlepattern="/.*\.(rpc.*)/";
preg_match($graphtitlepattern, $value, $graphtitle);
$hostpattern="/statsd\.(.*)\.rpc.*/";
preg_match($hostpattern, $value, $host);
//echo "in here for $value and $i - $host[1] - $graphtitle[1]<br>";


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
//            'title' => "$host[1] - $graphtitle[1]",
            'title' => "$host[1] - $COLORS[$colorid]",
			'metrics' => array("cactiStyle(alias(keepLastValue($value), \"$graphtitle[1]\"))"),
			'show_legend' => 1,
			'show_html_legend' => 1,
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

