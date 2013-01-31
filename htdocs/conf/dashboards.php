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
        'cacti' => 'testtab.php',
        'graphite' => 'testgraphite.php',
    ); 

    public static $DB_TABS = array(
        'PGBouncer' => 'tabs/example_pgbouncer.php',
        'PostgreSQL Queries' => 'tabs/example_postgresql_queries.php',
    );

    public static $DEPLOY_TABS = array(
        'FITB' => 'tabs/example_fitb.php',
        'New Relic' => 'tabs/example_newrelic.php',
    );

    public static $HADOOP_TABS = array(
        'Overview' => 'tabs/example_hadoop/overview.php',
        'DFS' => 'tabs/example_hadoop/dfs.php',
        'Jobs' => 'tabs/example_hadoop/jobs.php',
        'Java Process Metrics' => 'tabs/example_hadoop/java_process.php',
        'HBase' => 'tabs/example_hadoop/hbase.php',
    );

    public static $NETWORK_TABS = array(
        'FITB' => 'tabs/example_fitb.php',
        'Netstat' => 'tabs/example_netstat.php',
        'Mem info' => 'tabs/example_meminfo.php',
    );

    public static $TIME_TABS = array(
        'Time' => 'tabs/example_time.php',
    );

}
