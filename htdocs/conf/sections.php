<?php
require_once('phplib/Dashboard.php');
require_once('conf/dashboards.php');

$sections = array(
    'Application' => array(
        'Deploy' => Local_Dashboard::$DEPLOY_TABS,
    ),
    'Operations' => array(
        'Database' => Local_Dashboard::$DB_TABS,
        'Network' => Local_Dashboard::$NETWORK_TABS,
        'Chef' => array(
            'chef' => 'example_chef.php',
        ),
        'Hadoop' => Local_Dashboard::$HADOOP_TABS,
        'Util' => Local_Dashboard::$TIME_TABS,
    ),
);

