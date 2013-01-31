<?php
require_once 'phplib/Dashboard.php';

/** the title used for the page */
$title = 'yo graphite';

/** a short alphanumeric string (used for CSS) */
$namespace = 'chef';






$data = json_decode(file_get_contents("http://$graphite_server/metrics/index.json"));
//$matches = preg_grep("/.*kiwi.*rpc.*count.counters.*/", $data);
//$matches = preg_grep("/.*kiwi-app00.*rpc\.batch\.count\.counters.*/", $data);
$matches = preg_grep("/.*kiwi-app.*rpc\.batch\.count\.counters.count/", $data);

//$pattern="/.*kiwi-app00.*rpc\.batch\.count\.counters.count/";


echo "we are here<br>";
echo "<pre>";
//print_r($data);
//print_r($matches);


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
echo "<br>";
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

$graphs = array();
$i = 0;
foreach ($matches as $value) {
//echo "in here for $value and $i<br>";


$graphtitlepattern="/.*\.(rpc.*)/";
preg_match($graphtitlepattern, $value, $graphtitle);
$hostpattern="/statsd\.(.*)\.rpc.*/";
preg_match($hostpattern, $value, $host);
//echo "in here for $value and $i - $host[1] - $graphtitle[1]<br>";


	$graphs["$host[1] $graphtitle[1]"] = array(
        array(
            'type' => 'graphite',
            'title' => "$graphtitle[1]",
			'metrics' => array("cactiStyle(keepLastValue($value))"),
			'show_legend' => 0,
        ), 
    );
  $i++;
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

