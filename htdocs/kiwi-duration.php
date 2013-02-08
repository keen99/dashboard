<?php
require_once 'phplib/Dashboard.php';
require_once 'easod/functions.php';

require_once 'kiwi-templates.php';

 createGraphsFromTemplates("kiwiallrpcduration", "service", true, true);

if ( isset($_GET['count']) )
 createGraphsFromTemplates("kiwiallrpccounts", "service", true, true);


//1
//  group by service, each service and each host on it's own graph - err/count split
//createGraphsFromTemplates("kiwiallrpccounts", "service", false, false);
//5
//  group by service, each service and each host on it's own graph - err/count combined and sum
//createGraphsFromTemplates("kiwiallrpccounts", "service", true, false);
//2
//  group by service, aggregate each service (all hosts) onto one graph
//createGraphsFromTemplates("kiwiallrpccounts", "service", false, true);
//6
//  group by service, aggregate each service (all hosts) onto one graph and sum
//createGraphsFromTemplates("kiwiallrpccounts", "service", true, true);
//3
//  group by host, each service and each host on it's own graph - split err/count
//createGraphsFromTemplates("kiwiallrpccounts", "host", false, false);
//4 -- too large w/ all rpcs
//  group by host, aggregate each host (all services) onto one graph
//createGraphsFromTemplates("kiwiallrpccounts", "host", false, true);
//7
//  group by host, each service and each host on it's own graph - combined errr/count
//createGraphsFromTemplates("kiwiallrpccounts", "host", true, false);
//8
//  group by host, aggregate each host (all services) onto one graph
//createGraphsFromTemplates("kiwiallrpccounts", "host", true, true);





// printTimer('pre template');
//exit;
/** actually draws the page */
include 'phplib/template.php';
