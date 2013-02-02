<?php
require_once 'phplib/Dashboard.php';
require_once 'easod/functions.php';

/** the title used for the page */
$title = 'kiwi rpcs';
$namespace="";

addGraphTemplate(
	"kiwirpcs", 
	"kiwi rpcs", 
	"(^statsd)",
	"(.*.*)\.kiwi",
	"(rpc.*)",
	"(count\.counters\.count)"
);

//meh...
addGraphTemplate(
	"kiwitest", 
	"kiwi rpcs", 
	"(^statsd)",
	"(.*.*)\.kiwi",
	"(rpc\.auction.*)",
	"(count\.counters\.count)",
	array("keepLastValue(statsd.live-kiwi-app*.kiwi.*.rpc.auction.bid.count.counters.count)")
);

createGraphsFromTemplates("kiwirpcs", "host");
//createGraphsFromTemplates("kiwirpcs", "service");

//createGraphsFromTemplates("kiwitest");



/** actually draws the page */
include 'phplib/template.php';


