<?php
require_once 'phplib/Dashboard.php';

/** the title used for the page */
$title = 'yo graphite';

/** a short alphanumeric string (used for CSS) */
$namespace = 'chef';

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
$graphs = array(
    'Run Times' => array(
        array(
            'type' => 'graphite',
            'title' => 'Average Elapsed Times',
            'metrics' => array(
'sumSeries(keepLastValue(statsd.live-kiwi-app*.kiwi.9000.rpc.auction.bid.count.counters.count))' ,
'sumSeries(keepLastValue(statsd.live-kiwi-app*.kiwi.9000.rpc.auction.bid.errors.counters.count))'
            ),
//            'colors' => array('blue'),
            'width' => 440,
            'height' => 280,
        ), 
        array(
            'type' => 'graphite',
            'title' => 'Max Elapsed Times',
            'metrics' => array(
                'maxSeries(chef.runs.*.elapsed_time)',
            ),
            'colors' => array('blue'),
            'width' => 440,
            'height' => 280,
        ), 
        array(
            'type' => 'graphite',
            'title' => 'All Elapsed Times',
            'metrics' => array(
                'chef.runs.*.elapsed_time',
            ),
            'colors' => array('blue'),
            'width' => 440,
            'height' => 280,
        ), 
    ),

);

/** actually draws the page */
include 'phplib/template.php';
