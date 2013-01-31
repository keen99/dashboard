<?php

class Local_Dashboard {

    /** The $..._TABS arrays define which tabs are shown at the tops of various 
     * pages. They also control what is shown on the index page.
     *
     * Each entry is the name of the tab, and the URL to open.
     * URLs don't have to redirect to other dashboard pages, use them to go to 
     * external sites too! Want to add a link to the Hadoop DFS page? Easy!
     */

    public static $STUFF_TABS = array(
        'cacti' => 'tabs/testtab.php',
        'graphite' => 'tabs/testgraphite.php',
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
