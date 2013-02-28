<?php



class Local_Dashboard {

    /** The $..._TABS arrays define which tabs are shown at the tops of various 
     * pages. They also control what is shown on the index page.
     *
     * Each entry is the name of the tab, and the URL to open.
     * URLs don't have to redirect to other dashboard pages, use them to go to 
     * external sites too! Want to add a link to the Hadoop DFS page? Easy!
     */

//$viewperiod='1d';
//$viewurl='viewtemplate.php?time=' . $viewperiod . '&graphtemplate=';

// foreach ( $graphTemplates as $key => $value) { 
// 
// 	$link=$viewurl . $key;
// 
// 	echo "<a href='" . $link . "'>" . $value['sectiontitle'] . "</a>";
// //now add some optional versions...
// 	echo " <a href='".  $link . "&sum=true'>" . "[sum]</a>";
// 	echo " <a href='".  $link . "&agg=true'>" . "[agg]</a>";
// 	echo " <a href='".  $link . "&sum=true&agg=true'>" . "[both]</a><br>";



//    public static $DYNAMIC_TABS;

//	public $DYNAMIC_TABS=self::dothatshit();
	
	public function BuildIt() {

		require 'test-templates.php';
		$output=array();
		$types=array('normal', 'sum', 'agg', 'both');
		$groupbys=array('host', 'service');

		$viewperiod='1d';
		$viewurl='viewtemplate.php?time=' . $viewperiod . '&graphtemplate=';

		foreach ($groupbys as $groupby ) {
			foreach ( $graphTemplate as $key => $value) {
				$title=$value['sectiontitle'] . '(' . $groupby . ')';
				$output[$title]=array();
				foreach ( $types as $type ) {
					$link=$viewurl . $key;
					switch ($type) {
						case 'normal':
							$link=$link;
							//nothing
							break;
						case 'sum':
							$link.='&sum=true';
							break;
						case 'agg':
							$link.='&agg=true';					
							break;					
						case 'both':
							$link.='&sum=true&agg=true';
							break;
					}
					$output[$title][$type] = $link;
				}
			}	
		}
		return $output;	
	}
	
	public function DYNAMIC_TABS($type='normal') {
		//dont require once here - otherwise a second pass might not get it's data
//this shouldn't be hardcoded...
		require 'test-templates.php';
		$viewperiod='1d';
		$viewurl='viewtemplate.php?time=' . $viewperiod . '&graphtemplate=';
		foreach ( $graphTemplate as $key => $value) {
			$title=$value['sectiontitle'];
			$link=$viewurl . $key;
			switch ($type) {
				case 'normal':
					//nothing
				case 'sum':
					$link.='&sum=true';
				case 'agg':
					$link.='&agg=true';					
				case 'both':
					$link.='&sum=true&agg=true';
			}
			$graphTemplates[$title] = $link;
		}
		natcasesort($graphTemplates); // give these a nice sorted order	
		return $graphTemplates;
	}




    public static $KIWI_TABS = array(
                        'all rpcs, count' => 'kiwi-count.php?m=1&time=1d',
                        'all rpcs, duration' => 'kiwi-duration.php?m=1&time=1d',
    );

    public static $STUFF_TABS = array(
        'cacti' => 'testtab.php',
        'graphite' => 'testgraphite.php',
    ); 

    public static $DB_TABS = array(
        'PGBouncer' => 'example_pgbouncer.php',
        'PostgreSQL Queries' => 'example_postgresql_queries.php',
    );

    public static $DEPLOY_TABS = array(
        'FITB' => 'example_fitb.php',
        'New Relic' => 'example_newrelic.php',
    );

    public static $HADOOP_TABS = array(
        'Overview' => 'example_hadoop/overview.php',
        'DFS' => 'example_hadoop/dfs.php',
        'Jobs' => 'example_hadoop/jobs.php',
        'Java Process Metrics' => 'example_hadoop/java_process.php',
        'HBase' => 'example_hadoop/hbase.php',
    );

    public static $NETWORK_TABS = array(
        'FITB' => 'example_fitb.php',
        'Netstat' => 'example_netstat.php',
        'Mem info' => 'example_meminfo.php',
    );

    public static $TIME_TABS = array(
        'Time' => 'example_time.php',
    );

}
