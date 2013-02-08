<?php

require_once "../phplib/Dashboard.php";

$urls = array_values(array(
        'Overview' => 'overview.php',
        'DFS' => 'dfs.php',
        'Jobs' => 'jobs.php',
        'Java Process Metrics' => 'java_process.php',
        'HBase' => 'hbase.php',
    ) );

//Local_Dashboard::$HADOOP_TABS);
header('Location: ' . $urls[0]);
