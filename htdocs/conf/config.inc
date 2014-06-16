<?php

/*
	Override global setup in phplib/globals.php

for example:

$cacti_server = "cacti.example.com";
$chef_server = "chef.example.com";
$fitb_server = "fitb.example.com";
$ganglia_server = "ganglia.example.com";
$ganglia_server_dev = "ganglia.dev.example.com";
$graphite_server = "graphite.example.com";
$graphite_server_dev = "graphite.dev.example.com";
$splunk_server = "splunk.example.com";

*/

// // required config for examples, should be moved somewhere

// Hadoop name node 
$hadoopnn = "nn1.example.com";
/** Ganglia cluster which contains $hadoopnn */
$gangliacluster_nn = "HadoopNN";
/** Ganglia cluster which contains hadoop data nodes */
$gangliacluster_dn = "HadoopDN";

/** Servers hosting pgbouncer */
$pgbouncer_cluster_arr = array(
    '<ganglia db cluster name>' => array('name' => '<ganglia db cluster name>', 'machines' => 'db1.example.com'),
);





//ini_set('display_errors', true);
//ini_set('display_warnings', true);
