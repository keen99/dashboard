<?php

// I should have a 'config' file that overrides me puhlease


ini_set('display_errors', true);

require_once('Controls.php');
require_once('DeployConstants.php');
require_once('GraphConstants.php');
require_once('GraphFactory.php');
require_once('GraphiteHelper.php');
require_once('Graph/Cacti.php');
require_once('Graph/FITB.php');
require_once('Graph/Ganglia.php');
require_once('Graph/Graphite.php');
require_once('Graph/GraphitePercentage.php');
require_once('Graph/GraphitePie.php');
require_once('Graph/Graphlot.php');
require_once('Graph/NewRelic.php');
require_once('SplunkUtils.php');
require_once('Tabs.php');
require_once('TimingUtils.php');

/** Most of the dashboard configuration is done here.
 * If you don't use one of the services here, just leave the entry blank. It 
 * won't hurt it :-)
 */

$cacti_server = "monitor.easod.ea.comasdfasdfsfasdf";
$chef_server = "chef.example.com";
$fitb_server = "fitb.example.com";
$ganglia_server = "ganglia.example.com";
$ganglia_server_dev = "ganglia.dev.example.com";
$graphite_server = "ec2-184-73-211-106.compute-1.amazonaws.com";
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
require_once(dirname(__FILE__) . '/../config.php');


class Dashboard {
    /**
     * @param string $filename
     * @return string
     */
    public static function getTabUrl($filename) {
        return Tabs::getTabUrl($filename);
    }

    /**
     * @return array
     */
    public static function getTimes() {
        return array(
            '1h' => '1 hour',
            '2h' => '2 hours',
            '4h' => '4 hours',
            '12h' => '12 hours',
            '1d' => '1 day',
            '2d' => '2 days',
            '3d' => '3 days',
            '1w' => '1 week',
            '1m' => '1 month',
            '2m' => '2 months',
        );
    }

    /**
     * @param string $time
     * @return string formatted
     */
    public static function displayTime($time) {
        $units = array(
            'h' => 'hour',
            'd' => 'day',
            'w' => 'week',
            'm' => 'month',
            'y' => 'year',
        );

        if (empty($time)) {
            return;
        } else if (preg_match("/^(\d+)([a-z])/", strtolower(trim($time)), $m)) {
            list($t, $u) = self::_parseTime($time);
            return $t . ' ' . $units[$u] . (($t > 1) ? 's' : '');
        } else if (preg_match("/^(\d){10}$/", $time)) { // epoch time
            $default_tz = date_default_timezone_get();
            date_default_timezone_set("GMT");
            $date = date('D n/j G:i e',$time);
            date_default_timezone_set($default_tz);
            return $date;
        }

    }

    /**
     * @param string $time
     * @return int epoch seconds
     */
    public static function epochSecondsForTime($time) {
        list($t, $u) = self::_parseTime($time);
        $unit_seconds = 0;

        switch ($u) {
            case 'h':
                $unit_seconds = 3600;
                break;
            case 'd':
                $unit_seconds = 86400;
                break;
            case 'w':
                $unit_seconds = 86400 * 7;
                break;
            case 'm':
                $unit_seconds = 86400 * 30;
                break;
            case 'y':
                $unit_seconds = 86400 * 365;
                break;
        }

        return time() - ($t * $unit_seconds);
    }

    /**
     * @param string $time
     * @return array
     */
    private static function _parseTime($time) {
        preg_match("/^(\d+)([a-z])/", strtolower($time), $m);
        return array($m[1], $m[2]);
    }

    public static function hasGraphitePercentage($graphs) {
        foreach ((array)$graphs as $section) {
            foreach ((array)$section as $graph) {
                if ($graph['type'] == 'graphite' && isset($graph['numerator_metrics']) && isset($graph['denominator_metrics'])) {
                    return true;
                }
            }
        }

        return self::hasGraphType($graphs, 'graphite_percentage');
    }

    public static function hasGraphitePie($graphs) {
        return self::hasGraphType($graphs, 'graphite_pie');
    }

    public static function hasGraphType($graphs, $type) {
        foreach ((array)$graphs as $section) {
            foreach ((array)$section as $graph) {
                if (isset($graph['type']) && $graph['type'] == $type) {
                    return true;
                }
            }
        }

        return false;
    }
}
