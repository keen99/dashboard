<?php


require_once 'phplib/Dashboard.php';
require_once 'easod/functions.php';

require_once 'test-templates.php';

startTimer();




// not really what we want
// this is really view-graphs...

buildTemplateDropDown();
buildGraphOptions();

if ( $_GET['graphtemplate'] === 'empty' ) {
	echo '<div class=notice>please choose a graph template<br></div>';
	$graphs=array();
} else {
	createGraphsFromTemplates($_GET['graphtemplate'], $_GET['groupby'], $_GET['sum'], $_GET['agg']);
}



//this sets up a tab-navigation between pages.  also adds an "up to home" link
$tabs = Local_Dashboard::DYNAMIC_TABS();
$tab_url = Dashboard::getTabUrl(__FILE__);


// actually adds it right before the main graph frame.
// and adds html to the header
//$html_for_header=...

$html_for_header=generateListBox($graphs);


/** the title used for the page */
$title = $graphTemplate[$_GET['graphtemplate']]['sectiontitle'];
$namespace="";




 printTimer('pre template');
//exit;
/** actually draws the page */
include 'phplib/template.php';
