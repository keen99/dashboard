<?php
require_once('phplib/Dashboard.php');
require_once('conf/dashboards.php');

$sections = array(

	'build me a' => local_Dashboard::BuildIt(),
	

	'Dynamic Stuff' => array (
			'Templates' => Local_Dashboard::DYNAMIC_TABS(),
			'Sum' => Local_Dashboard::DYNAMIC_TABS(),
			'Agg' => Local_Dashboard::DYNAMIC_TABS(),
			'Both' => Local_Dashboard::DYNAMIC_TABS(),
	),


	'treKiwi' => array ( 
        '</div> <div> all counts ' => array(
        	'sum' => 'link',
        	'agg' => 'link',
        	'both' => 'link',
        ),
        'all duration ' => array(
        	'sum' => 'link',
        	'agg' => 'link',
        	'both' => 'link',

        ),        
	),

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

