<?php

ini_set('display_errors', true);


//lets add CWD to path for everything future
//this adds phplib
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));
//and this adds our root
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . "/../");




/** Most of the dashboard configuration is done here.
 * If you don't use one of the services here, just leave the entry blank. It 
 * won't hurt it :-)
 */

$cacti_server = "cacti.example.com";
$chef_server = "chef.example.com";
$fitb_server = "fitb.example.com";
$ganglia_server = "ganglia.example.com";
$ganglia_server_dev = "ganglia.dev.example.com";
$graphite_server = "graphite.example.com";
$graphite_server_dev = "graphite.dev.example.com";
$splunk_server = "splunk.example.com";

/** Hadoop name node */
$hadoopnn = "nn1.example.com";
/** Ganglia cluster which contains $hadoopnn */
$gangliacluster_nn = "HadoopNN";
/** Ganglia cluster which contains hadoop data nodes */
$gangliacluster_dn = "HadoopDN";

/** Servers hosting pgbouncer */
$pgbouncer_cluster_arr = array(
    '<gangla db cluster name>' => array('name' => '<ganglia db cluster name>', 'machines' => 'db1.example.com'),
);


// now pull in local overrides for the defaults
require_once(dirname(__FILE__) . '/../conf/config.php');

