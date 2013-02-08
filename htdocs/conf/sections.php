<?php
require_once('phplib/Dashboard.php');
require_once('conf/dashboards.php');

$sections = array(

	'Kiwi' => array ( 
        ' Live ' => Local_Dashboard::$KIWI_TABS,
	),
/*
    'My shtuf' => array(
        'Stuff' => Local_Dashboard::$STUFF_TABS,
    ),

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
*/
);

