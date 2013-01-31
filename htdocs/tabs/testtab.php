<?php

// the title used for the page
$title = 'Cacti Example';

require_once 'phplib/Dashboard.php';

$cacti_graphs = array(
       'thatthing' => array(
            'type' => 'graphite',
            'title' => 'Average Elapsed Times',
            'metrics' => array(
'sumSeries(keepLastValue(statsd.live-kiwi-app*.kiwi.9000.rpc.auction.bid.count.counters.count))' ,
'sumSeries(keepLastValue(statsd.live-kiwi-app*.kiwi.9000.rpc.auction.bid.errors.counters.count))'
            ),
//            'colors' => array('blue'),
            'width' => 603,
            'height' => 269,
        ), 
    'system01-cpu' => array(
            'type' => 'cacti',
            'metric' => '5776',
            'width' => 603,
            'height' => 269,
    ),
    'file02-nfs' => array(
            'type' => 'cacti',
            'metric' => '5777',
            'width' => 603,
            'height' => 269
    ),
    'system02-cpu' => array(
            'type' => 'cacti',
            'metric' => '5787',
            'width' => 603,
            'height' => 269
    ),
    'system02-nfs' => array(
            'type' => 'cacti',
            'metric' => '5788',
            'width' => 603,
            'height' => 269
    ),
);


$graphs = array(
    'System Utilisation' => $cacti_graphs,
);

include 'phplib/template.php';
