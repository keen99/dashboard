<?php
require_once('phplib/Dashboard.php');
require_once('dashboards.php');

$sections = array(

$sections = array(
    'My shtuf' => array(
        'Stuff' => Dashboard::$STUFF_TABS,
    ),

    'Application' => array(
        'Deploy' => Dashboard::$DEPLOY_TABS,
    ),
    'Operations' => array(
        'Database' => Dashboard::$DB_TABS,
        'Network' => Dashboard::$NETWORK_TABS,
        'Chef' => array(
            'chef' => '/example_chef.php',
        ),
        'Hadoop' => Dashboard::$HADOOP_TABS,
        'Util' => Dashboard::$TIME_TABS,
    ),
);

