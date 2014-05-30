<?php


require_once 'phplib/Dashboard.php';
require_once 'easod/functions.php';

require_once 'ls-templates.php';

startTimer();



// present list of graph templates.

$viewperiod='1d';
$viewurl='viewtemplate.php?time=' . $viewperiod . '&graphtemplate=';

foreach ( $graphTemplate as $key => $value) { 

	$link=$viewurl . $key;

	echo "<a href='" . $link . "'>" . $value['sectiontitle'] . "</a>";
//now add some optional versions...
	echo " <a href='".  $link . "&sum=true'>" . "[sum]</a>";
	echo " <a href='".  $link . "&agg=true'>" . "[agg]</a>";
	echo " <a href='".  $link . "&sum=true&agg=true'>" . "[both]</a><br>";



//	$graphTemplates[$key] = $value['sectiontitle']; 
}

//	natcasesort($graphTemplates); // give these a nice sorted order	